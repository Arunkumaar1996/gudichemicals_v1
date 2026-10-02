<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Formula;
use App\Models\InventoryBatch;
use App\Models\ProductionOrder;
use App\Models\Warehouse;
use App\Services\Production\ProductionService;
use Illuminate\Http\Request;

class ProductionOrderController extends Controller
{
    public function __construct(
        protected ProductionService $productionService
    ) {}

    public function index()
    {
        $orders = ProductionOrder::with(['formula', 'outputProduct', 'targetWarehouse', 'operator'])
            ->latest('order_date')
            ->paginate(15);

        return view('production.orders.index', compact('orders'));
    }

    public function create()
    {
        $formulas = Formula::where('is_active', true)->where('is_approved', true)
            ->with(['product', 'outputUnit'])
            ->get();
        $warehouses = Warehouse::where('is_active', true)->get();

        return view('production.orders.create', compact('formulas', 'warehouses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'formula_id' => ['required', 'exists:formulas,id'],
            'planned_qty' => ['required', 'numeric', 'gt:0'],
            'target_warehouse_id' => ['required', 'exists:warehouses,id'],
            'order_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $order = $this->productionService->createOrder(
            formulaId: $validated['formula_id'],
            plannedQty: (float)$validated['planned_qty'],
            targetWarehouseId: $validated['target_warehouse_id'],
            orderDate: $validated['order_date'],
            notes: $validated['notes'] ?? null,
            operatorId: auth()->id()
        );

        return redirect()->route('production.orders.show', $order->id)
            ->with('success', "Production Batch {$order->batch_number} created with scaled ingredient requirements.");
    }

    public function show(ProductionOrder $order)
    {
        $order->load([
            'formula.outputUnit',
            'outputProduct.unit',
            'targetWarehouse',
            'operator',
            'approver',
            'consumptions.product.unit',
            'consumptions.batch',
            'qualityChecks.tester',
            'batch',
        ]);

        return view('production.orders.show', compact('order'));
    }

    public function recordQc(Request $request, ProductionOrder $order)
    {
        $validated = $request->validate([
            'parameter_name' => ['required', 'string', 'max:100'],
            'standard_specification' => ['required', 'string', 'max:150'],
            'observed_value' => ['required', 'string', 'max:150'],
            'is_passed' => ['required', 'boolean'],
            'remarks' => ['nullable', 'string'],
        ]);

        $this->productionService->recordQualityCheck(
            order: $order,
            parameterName: $validated['parameter_name'],
            standardSpec: $validated['standard_specification'],
            observedValue: $validated['observed_value'],
            isPassed: (bool)$validated['is_passed'],
            remarks: $validated['remarks'] ?? null
        );

        return back()->with('success', 'Quality control check parameter recorded.');
    }

    public function finalize(Request $request, ProductionOrder $order)
    {
        $validated = $request->validate([
            'actual_qty' => ['required', 'numeric', 'gt:0'],
            'packaging_cost' => ['nullable', 'numeric', 'min:0'],
            'overhead_cost' => ['nullable', 'numeric', 'min:0'],
            'mfg_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'consumptions' => ['required', 'array'],
            'consumptions.*.consumption_id' => ['required', 'exists:production_consumptions,id'],
            'consumptions.*.actual_consumed_qty' => ['required', 'numeric', 'gt:0'],
        ]);

        $finalizedOrder = $this->productionService->finalizeBatch(
            order: $order,
            actualYieldQty: (float)$validated['actual_qty'],
            actualConsumptions: $validated['consumptions'],
            packagingCost: (float)($validated['packaging_cost'] ?? 0),
            overheadCost: (float)($validated['overhead_cost'] ?? 0),
            mfgDate: $validated['mfg_date'] ?? null,
            expiryDate: $validated['expiry_date'] ?? null,
            approvedBy: auth()->id()
        );

        return redirect()->route('production.orders.show', $finalizedOrder->id)
            ->with('success', "Batch {$finalizedOrder->batch_number} finalized! Raw materials deducted, finished goods credited, unit cost computed: ₹" . number_format($finalizedOrder->unit_production_cost, 2));
    }
}
