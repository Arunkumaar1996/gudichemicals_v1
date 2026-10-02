<?php

namespace App\Services\Production;

use App\Exceptions\ProductionException;
use App\Models\CompanySetting;
use App\Models\DocumentSequence;
use App\Models\Formula;
use App\Models\InventoryBatch;
use App\Models\ProductionConsumption;
use App\Models\ProductionOrder;
use App\Models\ProductionQualityCheck;
use App\Services\Inventory\InventoryService;
use Illuminate\Support\Facades\DB;

class ProductionService
{
    public function __construct(
        protected InventoryService $inventoryService
    ) {}

    /**
     * Create a new production order from a formula version.
     */
    public function createOrder(
        int $formulaId,
        float $plannedQty,
        int $targetWarehouseId,
        string $orderDate,
        ?string $notes = null,
        ?int $operatorId = null
    ): ProductionOrder {
        $formula = Formula::with(['items.ingredient', 'product', 'outputUnit'])->findOrFail($formulaId);

        if (!$formula->is_active || !$formula->is_approved) {
            throw new ProductionException('Cannot create a production order from an unapproved or inactive formula.');
        }

        $company = CompanySetting::current();
        $batchNumber = DocumentSequence::getNextSequence(
            documentType: 'batch',
            fyCode: $company->fy_code ?? '2026-27',
            prefix: 'BT'
        );

        // Freeze formula snapshot
        $snapshot = [
            'formula_id' => $formula->id,
            'formula_code' => $formula->formula_code,
            'formula_name' => $formula->name,
            'version' => $formula->version,
            'standard_batch_qty' => (float)$formula->standard_batch_qty,
            'output_unit' => $formula->outputUnit?->code,
            'expected_yield_pct' => (float)$formula->expected_yield_pct,
            'process_loss_pct' => (float)$formula->process_loss_pct,
            'ingredients' => $formula->items->map(function ($item) {
                return [
                    'ingredient_id' => $item->ingredient_product_id,
                    'ingredient_name' => $item->ingredient?->name,
                    'quantity' => (float)$item->quantity,
                    'unit_id' => $item->unit_id,
                    'unit_code' => $item->unit?->code,
                    'wastage_pct' => (float)$item->wastage_pct,
                ];
            })->toArray(),
        ];

        return DB::transaction(function () use (
            $batchNumber,
            $formula,
            $plannedQty,
            $targetWarehouseId,
            $orderDate,
            $snapshot,
            $notes,
            $operatorId
        ) {
            $order = ProductionOrder::create([
                'batch_number' => $batchNumber,
                'formula_id' => $formula->id,
                'output_product_id' => $formula->product_id,
                'target_warehouse_id' => $targetWarehouseId,
                'planned_qty' => $plannedQty,
                'actual_qty' => 0.0,
                'order_date' => $orderDate,
                'status' => 'draft',
                'formula_snapshot' => $snapshot,
                'operator_id' => $operatorId ?? auth()->id(),
                'notes' => $notes,
            ]);

            // Calculate scaled ingredient requirements
            $scale = $plannedQty / (float)$formula->standard_batch_qty;

            foreach ($formula->items as $item) {
                $scaledQty = round((float)$item->quantity * $scale, 4);
                $unitCost = (float)($item->ingredient?->purchase_cost ?? 0);

                ProductionConsumption::create([
                    'production_order_id' => $order->id,
                    'product_id' => $item->ingredient_product_id,
                    'planned_qty' => $scaledQty,
                    'actual_consumed_qty' => $scaledQty, // default to planned
                    'unit_cost' => $unitCost,
                    'total_cost' => round($scaledQty * $unitCost, 4),
                ]);
            }

            return $order;
        });
    }

    /**
     * Record Quality Control (QC) parameters for a production batch.
     */
    public function recordQualityCheck(
        ProductionOrder $order,
        string $parameterName,
        string $standardSpec,
        string $observedValue,
        bool $isPassed,
        ?string $remarks = null
    ): ProductionQualityCheck {
        return ProductionQualityCheck::create([
            'production_order_id' => $order->id,
            'parameter_name' => $parameterName,
            'standard_specification' => $standardSpec,
            'observed_value' => $observedValue,
            'is_passed' => $isPassed,
            'tested_by' => auth()->id(),
            'remarks' => $remarks,
        ]);
    }

    /**
     * Finalize the production batch atomically.
     * Consumes ingredients, updates inventory, creates finished goods batch, and computes unit costing.
     */
    public function finalizeBatch(
        ProductionOrder $order,
        float $actualYieldQty,
        array $actualConsumptions, // Array of ['consumption_id', 'actual_consumed_qty', 'batch_id']
        float $packagingCost = 0.0,
        float $overheadCost = 0.0,
        ?string $mfgDate = null,
        ?string $expiryDate = null,
        ?int $approvedBy = null
    ): ProductionOrder {
        return DB::transaction(function () use (
            $order,
            $actualYieldQty,
            $actualConsumptions,
            $packagingCost,
            $overheadCost,
            $mfgDate,
            $expiryDate,
            $approvedBy
        ) {
            $lockedOrder = ProductionOrder::where('id', $order->id)->lockForUpdate()->firstOrFail();

            if ($lockedOrder->status === 'completed') {
                throw new ProductionException('This production order has already been finalized.');
            }

            if ($lockedOrder->status === 'cancelled') {
                throw new ProductionException('Cannot finalize a cancelled production order.');
            }

            $totalMaterialCost = 0.0;

            // 1. Process Actual Consumptions & Deduct Stock
            foreach ($actualConsumptions as $cData) {
                $consumption = ProductionConsumption::findOrFail($cData['consumption_id']);
                $actualQty = (float)($cData['actual_consumed_qty'] ?? $consumption->planned_qty);
                $batchId = $cData['batch_id'] ?? null;

                // Deduct stock for consumed ingredient
                $movement = $this->inventoryService->deductStock(
                    productId: $consumption->product_id,
                    warehouseId: $lockedOrder->target_warehouse_id,
                    quantity: $actualQty,
                    movementType: 'production_consumption',
                    batchId: $batchId,
                    referenceType: ProductionOrder::class,
                    referenceId: $lockedOrder->id,
                    notes: "Consumed in Batch #{$lockedOrder->batch_number}",
                    userId: $approvedBy ?? auth()->id()
                );

                $unitCost = abs((float)$movement->unit_cost);
                $lineCost = round($actualQty * $unitCost, 4);

                $consumption->actual_consumed_qty = $actualQty;
                $consumption->batch_id = $batchId;
                $consumption->unit_cost = $unitCost;
                $consumption->total_cost = $lineCost;
                $consumption->save();

                $totalMaterialCost += $lineCost;
            }

            // 2. Compute Total and Unit Production Cost
            $totalCost = $totalMaterialCost + $packagingCost + $overheadCost;
            $unitCost = $actualYieldQty > 0 ? round($totalCost / $actualYieldQty, 4) : 0.0;

            // 3. Create or find Finished Goods Batch
            $mfg = $mfgDate ?? now()->toDateString();
            $exp = $expiryDate ?? now()->addYears(2)->toDateString();

            $fgBatch = InventoryBatch::create([
                'product_id' => $lockedOrder->output_product_id,
                'batch_number' => $lockedOrder->batch_number,
                'mfg_date' => $mfg,
                'expiry_date' => $exp,
                'cost_per_unit' => $unitCost,
                'is_active' => true,
            ]);

            // 4. Add Finished Product Output to Inventory
            $this->inventoryService->addStock(
                productId: $lockedOrder->output_product_id,
                warehouseId: $lockedOrder->target_warehouse_id,
                quantity: $actualYieldQty,
                unitCost: $unitCost,
                movementType: 'production_output',
                batchId: $fgBatch->id,
                referenceType: ProductionOrder::class,
                referenceId: $lockedOrder->id,
                notes: "Output of Batch #{$lockedOrder->batch_number}",
                userId: $approvedBy ?? auth()->id()
            );

            // 5. Update Order Status and Costing
            $lockedOrder->actual_qty = $actualYieldQty;
            $lockedOrder->completion_date = now();
            $lockedOrder->status = 'completed';
            $lockedOrder->total_material_cost = $totalMaterialCost;
            $lockedOrder->packaging_cost = $packagingCost;
            $lockedOrder->overhead_cost = $overheadCost;
            $lockedOrder->unit_production_cost = $unitCost;
            $lockedOrder->batch_id = $fgBatch->id;
            $lockedOrder->approved_by = $approvedBy ?? auth()->id();
            $lockedOrder->save();

            return $lockedOrder;
        });
    }
}
