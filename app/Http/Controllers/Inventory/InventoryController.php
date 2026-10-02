<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\InventoryBatch;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\Warehouse;
use App\Services\Inventory\InventoryService;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function index(Request $request)
    {
        $warehouseId = $request->warehouse_id;
        $categoryId = $request->category_id;
        $search = $request->search;

        $query = Product::with(['unit', 'category', 'stockBalances' => function ($q) use ($warehouseId) {
            if ($warehouseId) {
                $q->where('warehouse_id', $warehouseId);
            }
            $q->with('batch');
        }]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        if ($request->filled('item_type')) {
            $query->where('item_type', $request->item_type);
        }

        // Low stock filter (items with reorder_level > 0 and stock <= reorder_level)
        if ($request->boolean('low_stock')) {
            $query->where('reorder_level', '>', 0)
                  ->whereRaw('(SELECT COALESCE(SUM(quantity), 0) FROM stock_balances WHERE stock_balances.product_id = products.id) <= products.reorder_level');
        }

        $lowStockCount = Product::where('is_active', true)
            ->where('reorder_level', '>', 0)
            ->whereRaw('(SELECT COALESCE(SUM(quantity), 0) FROM stock_balances WHERE stock_balances.product_id = products.id) <= products.reorder_level')
            ->count();

        $products = $query->orderBy('name')->paginate(20)->withQueryString();
        $warehouses = Warehouse::where('is_active', true)->get();
        $categories = ProductCategory::where('is_active', true)->get();

        return view('inventory.index', compact('products', 'warehouses', 'categories', 'lowStockCount'));
    }

    /**
     * Quick update for product low stock reorder threshold.
     */
    public function updateReorderLevel(Request $request, Product $product)
    {
        $validated = $request->validate([
            'reorder_level' => ['required', 'numeric', 'min:0'],
        ]);

        $product->update([
            'reorder_level' => $validated['reorder_level'],
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Low stock threshold for {$product->name} updated to {$product->reorder_level} {$product->unit?->code}.",
                'reorder_level' => (float)$product->reorder_level,
            ]);
        }

        return back()->with('success', "Low stock threshold for {$product->name} updated to {$product->reorder_level} {$product->unit?->code}.");
    }

    public function ledger(Request $request)
    {
        $query = StockMovement::with(['product.unit', 'warehouse', 'batch', 'creator'])
            ->latest('movement_date');

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('warehouse_id')) {
            $query->where('warehouse_id', $request->warehouse_id);
        }

        if ($request->filled('movement_type')) {
            $query->where('movement_type', $request->movement_type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('movement_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('movement_date', '<=', $request->to_date);
        }

        $movements = $query->paginate(25)->withQueryString();
        $products = Product::orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('inventory.ledger', compact('movements', 'products', 'warehouses'));
    }

    public function createOpeningStock()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('inventory.opening_stock', compact('products', 'warehouses'));
    }

    public function storeOpeningStock(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'quantity' => ['required', 'numeric', 'gt:0'],
            'unit_cost' => ['required', 'numeric', 'min:0'],
            'batch_number' => ['nullable', 'string', 'max:50'],
            'expiry_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $batchId = null;
        if (!empty($validated['batch_number'])) {
            $batch = InventoryBatch::firstOrCreate(
                ['product_id' => $validated['product_id'], 'batch_number' => $validated['batch_number']],
                [
                    'expiry_date' => $validated['expiry_date'] ?? null,
                    'cost_per_unit' => $validated['unit_cost'],
                    'is_active' => true,
                ]
            );
            $batchId = $batch->id;
        }

        $this->inventoryService->addStock(
            productId: $validated['product_id'],
            warehouseId: $validated['warehouse_id'],
            quantity: (float)$validated['quantity'],
            unitCost: (float)$validated['unit_cost'],
            movementType: 'opening_stock',
            batchId: $batchId,
            notes: $validated['notes'] ?? 'Initial Opening Stock Entry',
            userId: auth()->id()
        );

        return redirect()->route('inventory.index')->with('success', 'Opening stock successfully recorded in the stock ledger.');
    }
}
