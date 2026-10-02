<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\DocumentSequence;
use App\Models\SalesInvoice;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Services\Inventory\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesReturnController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function index()
    {
        $returns = SalesReturn::with(['salesInvoice', 'customer', 'creator', 'items.product'])
            ->latest('return_date')
            ->paginate(15);

        return view('sales.returns.index', compact('returns'));
    }

    public function create(Request $request)
    {
        $selectedInvoice = null;
        if ($request->filled('invoice_id')) {
            $selectedInvoice = SalesInvoice::with(['customer', 'items.product', 'items.unit'])->find($request->invoice_id);
        }

        $recentInvoices = SalesInvoice::with('customer')->latest()->take(25)->get();

        return view('sales.returns.create', compact('selectedInvoice', 'recentInvoices'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sales_invoice_id' => ['required', 'exists:sales_invoices,id'],
            'return_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:255'],
            'refund_status' => ['required', 'in:refunded,credited_to_account'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.condition' => ['required', 'in:saleable,damaged'],
        ]);

        $invoice = SalesInvoice::findOrFail($validated['sales_invoice_id']);
        $company = CompanySetting::current();
        $returnNumber = DocumentSequence::getNextSequence('credit_note', $company->fy_code ?? '2026-27', 'CRN');

        $return = DB::transaction(function () use ($validated, $invoice, $returnNumber) {
            $totalRefund = 0.0;

            $salesReturn = SalesReturn::create([
                'return_number' => $returnNumber,
                'sales_invoice_id' => $invoice->id,
                'customer_id' => $invoice->customer_id,
                'return_date' => $validated['return_date'],
                'total_refund_amount' => 0.0,
                'refund_status' => $validated['refund_status'],
                'status' => 'approved',
                'reason' => $validated['reason'],
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                $qty = (float)$item['quantity'];
                $price = (float)$item['unit_price'];
                $lineTotal = round($qty * $price, 2);

                SalesReturnItem::create([
                    'sales_return_id' => $salesReturn->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $qty,
                    'condition' => $item['condition'],
                    'unit_price' => $price,
                    'line_total' => $lineTotal,
                ]);

                // Stock re-entry: increase stock ONLY if accepted back into saleable inventory
                if ($item['condition'] === 'saleable') {
                    $this->inventoryService->addStock(
                        productId: $item['product_id'],
                        warehouseId: $invoice->warehouse_id,
                        quantity: $qty,
                        unitCost: $price,
                        movementType: 'sales_return',
                        referenceType: SalesReturn::class,
                        referenceId: $salesReturn->id,
                        notes: "Return Note #{$returnNumber}",
                        userId: auth()->id()
                    );
                }

                $totalRefund += $lineTotal;
            }

            $salesReturn->total_refund_amount = $totalRefund;
            $salesReturn->save();

            // Adjust Customer Balance if credited to account
            if ($validated['refund_status'] === 'credited_to_account') {
                $customer = Customer::find($invoice->customer_id);
                if ($customer) {
                    $customer->current_balance = max(0, (float)$customer->current_balance - $totalRefund);
                    $customer->save();
                }
            }

            return $salesReturn;
        });

        return redirect()->route('returns.index')
            ->with('success', "Sales Return Credit Note {$returnNumber} recorded successfully.");
    }
}
