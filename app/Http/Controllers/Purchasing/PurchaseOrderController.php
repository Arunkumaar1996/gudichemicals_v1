<?php

namespace App\Http\Controllers\Purchasing;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Unit;
use App\Models\Vendor;
use App\Models\Warehouse;
use App\Services\Purchasing\PurchasingService;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct(
        protected PurchasingService $purchasingService
    ) {}

    public function index()
    {
        abort_if(!auth()->user()->can('purchases.view'), 403, 'Access Denied: You do not have permission to view chemical purchase orders.');

        $orders = PurchaseOrder::with(['vendor', 'warehouse', 'creator', 'items.product'])
            ->latest('order_date')
            ->paginate(15);

        return view('purchases.orders.index', compact('orders'));
    }

    public function create()
    {
        abort_if(!auth()->user()->can('purchases.create'), 403, 'Access Denied: You do not have permission to create purchase orders.');

        $vendors = Vendor::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::where('is_active', true)->with('unit')->orderBy('name')->get();

        return view('purchases.orders.create', compact('vendors', 'warehouses', 'products'));
    }

    public function store(Request $request)
    {
        abort_if(!auth()->user()->can('purchases.create'), 403, 'Access Denied: You do not have permission to save purchase orders.');
        $validated = $request->validate([
            'vendor_id' => ['required', 'exists:vendors,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'order_date' => ['required', 'date'],
            'expected_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.unit_id' => ['required', 'exists:units,id'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.tax_rate' => ['required', 'numeric', 'min:0'],
        ]);

        $po = $this->purchasingService->createPurchaseOrder(
            vendorId: $validated['vendor_id'],
            warehouseId: $validated['warehouse_id'],
            orderDate: $validated['order_date'],
            expectedDate: $validated['expected_date'] ?? null,
            items: $validated['items'],
            notes: $validated['notes'] ?? null,
            userId: auth()->id()
        );

        return redirect()->route('purchases.orders.show', $po->id)
            ->with('success', "Purchase Order {$po->po_number} created successfully.");
    }

    public function show(PurchaseOrder $order)
    {
        abort_if(!auth()->user()->can('purchases.view'), 403, 'Access Denied: You do not have permission to view purchase order details.');

        $order->load(['vendor', 'warehouse', 'items.product', 'items.unit', 'goodsReceipts']);
        return view('purchases.orders.show', compact('order'));
    }

    public function approve(PurchaseOrder $order)
    {
        abort_if(!auth()->user()->can('purchases.approve'), 403, 'Access Denied: You do not have permission to authorize or approve purchase orders.');

        if ($order->status === 'draft') {
            $order->status = 'approved';
            $order->approved_by = auth()->id();
            $order->save();
            return back()->with('success', "Purchase Order {$order->po_number} approved.");
        }
        return back()->with('error', 'Cannot approve an order that is not in draft status.');
    }
}
