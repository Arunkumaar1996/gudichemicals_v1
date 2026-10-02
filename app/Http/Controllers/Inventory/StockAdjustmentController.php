<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use App\Models\CompanySetting;
use App\Models\DocumentSequence;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\Warehouse;
use App\Services\Inventory\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    public function index()
    {
        $adjustments = StockAdjustment::with(['warehouse', 'creator', 'approver', 'items.product'])
            ->latest('adjustment_date')
            ->paginate(15);

        return view('inventory.adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $warehouses = Warehouse::where('is_active', true)->get();
        $products = Product::where('is_active', true)->with('unit')->orderBy('name')->get();

        return view('inventory.adjustments.create', compact('warehouses', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'warehouse_id' => ['required', 'exists:warehouses,id'],
            'adjustment_date' => ['required', 'date'],
            'reason' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.type' => ['required', 'in:add,subtract'],
            'items.*.quantity' => ['required', 'numeric', 'gt:0'],
        ]);

        $company = CompanySetting::current();
        $adjNumber = DocumentSequence::getNextSequence('receipt', $company->fy_code ?? '2026-27', 'ADJ');

        $adj = DB::transaction(function () use ($validated, $adjNumber) {
            $adjustment = StockAdjustment::create([
                'adjustment_number' => $adjNumber,
                'warehouse_id' => $validated['warehouse_id'],
                'adjustment_date' => $validated['adjustment_date'],
                'reason' => $validated['reason'],
                'status' => 'draft',
                'created_by' => auth()->id(),
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                $prod = Product::find($item['product_id']);
                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'product_id' => $item['product_id'],
                    'type' => $item['type'],
                    'quantity' => (float)$item['quantity'],
                    'unit_cost' => (float)($prod?->purchase_cost ?? 0),
                ]);
            }

            return $adjustment;
        });

        // Automatically post adjustment
        $this->inventoryService->postAdjustment($adj);

        return redirect()->route('inventory.adjustments.index')->with('success', "Stock adjustment {$adjNumber} approved and posted to stock ledger.");
    }
}
