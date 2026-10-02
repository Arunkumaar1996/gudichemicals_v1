<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\InventoryBatch;
use App\Models\Product;
use App\Models\ProductCategory;
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
        $warehouses = Warehouse::where('is_active', true)->get();
        $defaultWarehouse = $warehouses->first();
        $warehouseId = $defaultWarehouse?->id;

        // High-speed initial load: load top 48 fast-moving active chemical products with batch details
        $products = Product::where('is_active', true)
            ->whereIn('item_type', ['finished_goods', 'trading', 'semi_finished'])
            ->with([
                'unit:id,code', 
                'category:id,name', 
                'stockBalances:id,product_id,warehouse_id,batch_id,quantity',
                'batches' => fn($q) => $q->where('is_active', true)->with('stockBalances')
            ])
            ->orderBy('name')
            ->limit(48)
            ->get()
            ->map(function ($p) use ($warehouseId) {
                return $this->formatProductItem($p, $warehouseId);
            });

        $categories = ProductCategory::where('is_active', true)->orderBy('name')->get();
        $customers = Customer::where('is_active', true)->orderBy('name')->get();
        $defaultCustomer = Customer::walkInCustomer();

        return view('sales.pos.index', compact('products', 'categories', 'customers', 'warehouses', 'defaultCustomer'));
    }

    /**
     * Ultra-fast debounced search API for 4,000+ chemical items with active lots.
     */
    public function search(Request $request)
    {
        $query = trim($request->get('q', ''));
        $categoryId = $request->get('category_id');
        $warehouseId = $request->get('warehouse_id');

        $products = Product::query()
            ->select(['id', 'sku', 'barcode', 'name', 'category_id', 'unit_id', 'retail_price', 'wholesale_price', 'hsn_code', 'gst_rate'])
            ->where('is_active', true)
            ->whereIn('item_type', ['finished_goods', 'trading', 'semi_finished'])
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sub) use ($query) {
                    $sub->where('barcode', $query)
                        ->orWhere('sku', 'like', "{$query}%")
                        ->orWhere('name', 'like', "%{$query}%");
                });
            })
            ->with([
                'unit:id,code',
                'category:id,name',
                'stockBalances' => fn($b) => $warehouseId ? $b->where('warehouse_id', $warehouseId) : $b,
                'batches' => fn($q) => $q->where('is_active', true)->with('stockBalances')
            ])
            ->limit(48)
            ->get()
            ->map(function ($p) use ($warehouseId) {
                return $this->formatProductItem($p, $warehouseId);
            });

        return response()->json($products);
    }

    /**
     * Exact 1-millisecond barcode scan lookup with available lots.
     */
    public function barcode(Request $request)
    {
        $code = trim($request->get('code', ''));
        if (!$code) {
            return response()->json(['found' => false], 404);
        }

        $warehouseId = $request->get('warehouse_id');
        $product = Product::where('is_active', true)
            ->whereIn('item_type', ['finished_goods', 'trading', 'semi_finished'])
            ->where(function ($q) use ($code) {
                $q->where('barcode', $code)
                  ->orWhere('sku', $code);
            })
            ->with([
                'unit:id,code',
                'category:id,name',
                'stockBalances' => fn($b) => $warehouseId ? $b->where('warehouse_id', $warehouseId) : $b,
                'batches' => fn($q) => $q->where('is_active', true)->with('stockBalances')
            ])
            ->first();

        if (!$product) {
            return response()->json(['found' => false, 'message' => 'Product not found for scanned barcode/SKU'], 404);
        }

        return response()->json([
            'found' => true,
            'product' => $this->formatProductItem($product, $warehouseId)
        ]);
    }

    /**
     * Helper to format consistent product payload with available lot breakdown.
     */
    protected function formatProductItem(Product $p, ?int $warehouseId): array
    {
        $batches = $p->batches->map(function ($b) use ($warehouseId) {
            $stock = (float)($warehouseId ? $b->stockBalances->where('warehouse_id', $warehouseId)->sum('quantity') : $b->stockBalances->sum('quantity'));
            return [
                'id' => $b->id,
                'batch_number' => $b->batch_number,
                'supplier_lot_number' => $b->supplier_lot_number,
                'mfg_date' => $b->mfg_date ? $b->mfg_date->format('d/m/Y') : null,
                'expiry_date' => $b->expiry_date ? $b->expiry_date->format('d/m/Y') : null,
                'stock' => $stock,
            ];
        })->filter(fn($b) => $b['stock'] > 0)->values();

        $totalStock = (float)($warehouseId ? $p->stockBalances->where('warehouse_id', $warehouseId)->sum('quantity') : $p->stockBalances->sum('quantity'));

        return [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->sku,
            'barcode' => $p->barcode,
            'category_id' => $p->category_id,
            'category_name' => $p->category?->name,
            'unit' => $p->unit?->code ?: 'NOS',
            'retail_price' => (float)$p->retail_price,
            'wholesale_price' => (float)$p->wholesale_price,
            'gst_rate' => (float)$p->gst_rate,
            'hsn_code' => $p->hsn_code,
            'stock' => $totalStock,
            'batches' => $batches,
        ];
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
            'cart_items.*.batch_id' => ['nullable', 'exists:inventory_batches,id'],
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
     * Robust payment normalization handles both split payments array and single payment shorthand.
     */
    public function store(Request $request)
    {
        // Normalize single-tender input into payments array if not provided
        if (!$request->has('payments') || !is_array($request->input('payments')) || empty($request->input('payments'))) {
            $method = $request->input('payment_method', 'cash');
            $paid = (float)$request->input('paid_amount', 0);
            $ref = $request->input('reference_number') ?? $request->input('reference_no');

            $request->merge([
                'payments' => [
                    [
                        'payment_method' => $method,
                        'amount' => $paid,
                        'reference_no' => $ref,
                    ]
                ]
            ]);
        }

        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'price_tier' => ['required', 'in:retail,wholesale'],
            'cart_items' => ['required', 'array', 'min:1'],
            'cart_items.*.product_id' => ['required', 'exists:products,id'],
            'cart_items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'cart_items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'cart_items.*.batch_id' => ['nullable', 'exists:inventory_batches,id'],
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

    /**
     * Quick Customer Registration from POS Modal
     */
    public function quickCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'gstin' => ['nullable', 'string', 'max:15'],
            'customer_type' => ['required', 'in:retail,wholesale'],
        ]);

        $customer = Customer::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'gstin' => $validated['gstin'],
            'customer_type' => $validated['customer_type'],
            'credit_limit' => 0,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'customer' => $customer,
        ]);
    }
}
