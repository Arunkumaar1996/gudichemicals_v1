<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\GoodsReceipt;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use App\Models\Warehouse;
use App\Services\Purchasing\PurchasingService;
use Illuminate\Http\Request;

class GoodsReceiptController extends Controller
{
    public function __construct(
        protected PurchasingService $purchasingService
    ) {}

    public function index()
    {
        $receipts = GoodsReceipt::with(['vendor', 'warehouse', 'purchaseOrder', 'receiver', 'items.product'])
            ->latest('receipt_date')
            ->paginate(15);

        return view('purchases.grn.index', compact('receipts'));
    }

    public function create(Request $request)
    {
        $vendors = Vendor::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        $selectedPo = null;
        if ($request->filled('po_id')) {
            $selectedPo = PurchaseOrder::with(['items.product', 'items.unit'])->find($request->po_id);
        }

        return view('purchases.grn.create', compact('vendors', 'warehouses', 'products', 'selectedPo'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor_id' => ['required', 'exists:vendors,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'receipt_date' => ['required', 'date'],
            'purchase_order_id' => ['nullable', 'exists:purchase_orders,id'],
            'supplier_challan_no' => ['nullable', 'string', 'max:100'],
            'supplier_invoice_no' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_cost' => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['required', 'numeric', 'min:0'],
            'items.*.batch_number' => ['nullable', 'string', 'max:50'],
            'items.*.supplier_lot_number' => ['nullable', 'string', 'max:50'],
            'items.*.mfg_date' => ['nullable', 'date'],
            'items.*.expiry_date' => ['nullable', 'date'],
            'items.*.purchase_order_item_id' => ['nullable', 'exists:purchase_order_items,id'],
        ]);

        $grn = $this->purchasingService->postGoodsReceipt(
            vendorId: $validated['vendor_id'],
            warehouseId: $validated['warehouse_id'],
            receiptDate: $validated['receipt_date'],
            items: $validated['items'],
            purchaseOrderId: $validated['purchase_order_id'] ?? null,
            supplierChallanNo: $validated['supplier_challan_no'] ?? null,
            supplierInvoiceNo: $validated['supplier_invoice_no'] ?? null,
            notes: $validated['notes'] ?? null,
            userId: auth()->id()
        );

        return redirect()->route('purchases.grn.show', $grn->id)
            ->with('success', "Goods Receipt Note {$grn->grn_number} posted and stock added to inventory.");
    }

    public function show(GoodsReceipt $receipt)
    {
        $receipt->load(['vendor', 'warehouse', 'purchaseOrder', 'receiver', 'items.product.unit']);
        return view('purchases.grn.show', compact('receipt'));
    }
}
