<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\SalesInvoice;
use App\Models\Warehouse;
use App\Services\Sales\InvoicePostingService;
use Illuminate\Http\Request;

class PosController extends Controller
{
    public function __construct(
        protected InvoicePostingService $invoicePostingService
    ) {}

    public function index()
    {
        $products = Product::where('is_active', true)
            ->whereIn('item_type', ['finished_goods', 'trading', 'semi_finished'])
            ->with(['unit', 'category', 'stockBalances'])
            ->orderBy('name')
            ->get();

        $categories = ProductCategory::where('is_active', true)->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $defaultCustomer = Customer::walkInCustomer();

        return view('sales.pos.index', compact('products', 'categories', 'customers', 'warehouses', 'defaultCustomer'));
    }

    /**
     * AJAX endpoint to recalculate cart subtotals, GST, and promotions on the fly.
     */
    public function calculate(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'price_tier' => ['required', 'in:retail,wholesale'],
            'cart_items' => ['required', 'array'],
            'cart_items.*.product_id' => ['required', 'exists:products,id'],
            'cart_items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'cart_items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $calculation = $this->invoicePostingService->calculatePreview(
            rawCartItems: $validated['cart_items'],
            customerId: $validated['customer_id'],
            priceTier: $validated['price_tier']
        );

        return response()->json($calculation);
    }

    /**
     * Post and finalize invoice from POS.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'price_tier' => ['required', 'in:retail,wholesale'],
            'cart_items' => ['required', 'array', 'min:1'],
            'cart_items.*.product_id' => ['required', 'exists:products,id'],
            'cart_items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'cart_items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'payments' => ['required', 'array', 'min:1'],
            'payments.*.amount' => ['required', 'numeric', 'min:0'],
            'payments.*.payment_method' => ['required', 'in:cash,upi,card,bank_transfer,credit'],
            'payments.*.reference_no' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $invoice = $this->invoicePostingService->postInvoice(
            rawCartItems: $validated['cart_items'],
            customerId: $validated['customer_id'],
            warehouseId: $validated['warehouse_id'],
            priceTier: $validated['price_tier'],
            payments: $validated['payments'],
            notes: $validated['notes'] ?? null,
            userId: auth()->id()
        );

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'print_url' => route('invoices.print', $invoice->id),
                'pdf_url' => route('invoices.pdf', $invoice->id),
            ]);
        }

        return redirect()->route('invoices.show', $invoice->id)
            ->with('success', "Invoice {$invoice->invoice_number} posted successfully.");
    }
}
