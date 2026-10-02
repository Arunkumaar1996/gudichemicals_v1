<?php

namespace App\Services\Inventory;

use App\Exceptions\InsufficientStockException;
use App\Models\InventoryBatch;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Add stock to inventory within a database transaction.
     */
    public function addStock(
        int $productId,
        int $warehouseId,
        float $quantity,
        float $unitCost,
        string $movementType,
        ?int $batchId = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null,
        ?int $userId = null
    ): StockMovement {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity to add must be greater than zero.');
        }

        return DB::transaction(function () use (
            $productId,
            $warehouseId,
            $quantity,
            $unitCost,
            $movementType,
            $batchId,
            $referenceType,
            $referenceId,
            $notes,
            $userId
        ) {
            $balance = StockBalance::where('product_id', $productId)
                ->where('warehouse_id', $warehouseId)
                ->where('batch_id', $batchId)
                ->lockForUpdate()
                ->first();

            if (!$balance) {
                $balance = StockBalance::create([
                    'product_id' => $productId,
                    'warehouse_id' => $warehouseId,
                    'batch_id' => $batchId,
                    'quantity' => 0.0000,
                    'reserved_quantity' => 0.0000,
                ]);
                $balance = StockBalance::where('id', $balance->id)->lockForUpdate()->first();
            }

            $newQty = (float)$balance->quantity + $quantity;
            $balance->quantity = $newQty;
            $balance->save();

            return StockMovement::create([
                'movement_date' => now(),
                'product_id' => $productId,
                'warehouse_id' => $warehouseId,
                'batch_id' => $batchId,
                'movement_type' => $movementType,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'quantity' => $quantity, // positive
                'unit_cost' => $unitCost,
                'balance_after' => $newQty,
                'notes' => $notes,
                'created_by' => $userId ?? auth()->id(),
            ]);
        });
    }

    /**
     * Deduct stock from inventory with pessimistic lock and non-negative guard.
     */
    public function deductStock(
        int $productId,
        int $warehouseId,
        float $quantity,
        string $movementType,
        ?int $batchId = null,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?string $notes = null,
        ?int $userId = null,
        bool $allowNegative = false
    ): StockMovement {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity to deduct must be greater than zero.');
        }

        return DB::transaction(function () use (
            $productId,
            $warehouseId,
            $quantity,
            $movementType,
            $batchId,
            $referenceType,
            $referenceId,
            $notes,
            $userId,
            $allowNegative
        ) {
            $product = Product::find($productId);
            $prodName = $product ? $product->name : "Product #{$productId}";

            // 1. If a specific batch is requested:
            if ($batchId !== null) {
                $balance = StockBalance::where('product_id', $productId)
                    ->where('warehouse_id', $warehouseId)
                    ->where('batch_id', $batchId)
                    ->lockForUpdate()
                    ->first();

                $currentStock = $balance ? (float)$balance->quantity : 0.0;

                if (!$allowNegative && $currentStock < $quantity) {
                    throw new InsufficientStockException("Insufficient stock for {$prodName}. Available: {$currentStock}, Requested: {$quantity}.");
                }

                if (!$balance) {
                    $balance = StockBalance::create([
                        'product_id' => $productId,
                        'warehouse_id' => $warehouseId,
                        'batch_id' => $batchId,
                        'quantity' => 0.0000,
                        'reserved_quantity' => 0.0000,
                    ]);
                    $balance = StockBalance::where('id', $balance->id)->lockForUpdate()->first();
                }

                $newQty = (float)$balance->quantity - $quantity;
                $balance->quantity = $newQty;
                $balance->save();

                $batch = InventoryBatch::find($batchId);
                $unitCost = $batch ? (float)$batch->cost_per_unit : 0.0;
                if ($unitCost <= 0) {
                    $unitCost = $product ? (float)$product->purchase_cost : 0.0;
                }

                return StockMovement::create([
                    'movement_date' => now(),
                    'product_id' => $productId,
                    'warehouse_id' => $warehouseId,
                    'batch_id' => $batchId,
                    'movement_type' => $movementType,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'quantity' => -$quantity,
                    'unit_cost' => $unitCost,
                    'balance_after' => $newQty,
                    'notes' => $notes,
                    'created_by' => $userId ?? auth()->id(),
                ]);
            }

            // 2. If batchId is NULL: Check total available stock across all batches and unbatched
            $totalAvailable = (float) StockBalance::where('product_id', $productId)
                ->where('warehouse_id', $warehouseId)
                ->sum('quantity');

            if (!$allowNegative && $totalAvailable < $quantity) {
                throw new InsufficientStockException("Insufficient stock for {$prodName}. Available: {$totalAvailable}, Requested: {$quantity}.");
            }

            // Check if unbatched balance alone is sufficient
            $unbatchedBalance = StockBalance::where('product_id', $productId)
                ->where('warehouse_id', $warehouseId)
                ->whereNull('batch_id')
                ->lockForUpdate()
                ->first();

            if ($unbatchedBalance && (float)$unbatchedBalance->quantity >= $quantity) {
                $newQty = (float)$unbatchedBalance->quantity - $quantity;
                $unbatchedBalance->quantity = $newQty;
                $unbatchedBalance->save();

                $unitCost = $product ? (float)$product->purchase_cost : 0.0;

                return StockMovement::create([
                    'movement_date' => now(),
                    'product_id' => $productId,
                    'warehouse_id' => $warehouseId,
                    'batch_id' => null,
                    'movement_type' => $movementType,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'quantity' => -$quantity,
                    'unit_cost' => $unitCost,
                    'balance_after' => $newQty,
                    'notes' => $notes,
                    'created_by' => $userId ?? auth()->id(),
                ]);
            }

            // Otherwise, allocate across active batches FIFO (oldest expiry or mfg date first)
            $batchBalances = StockBalance::where('product_id', $productId)
                ->where('warehouse_id', $warehouseId)
                ->whereNotNull('batch_id')
                ->where('quantity', '>', 0)
                ->with('batch')
                ->lockForUpdate()
                ->get()
                ->sortBy(function ($sb) {
                    return $sb->batch?->expiry_date?->timestamp ?? $sb->batch?->mfg_date?->timestamp ?? $sb->batch_id;
                });

            $remainingQty = $quantity;
            $lastMovement = null;

            foreach ($batchBalances as $bb) {
                if ($remainingQty <= 0) break;
                $deductQty = min((float)$bb->quantity, $remainingQty);
                $newQty = (float)$bb->quantity - $deductQty;
                $bb->quantity = $newQty;
                $bb->save();

                $unitCost = $bb->batch ? (float)$bb->batch->cost_per_unit : ((float)($product?->purchase_cost ?? 0.0));

                $lastMovement = StockMovement::create([
                    'movement_date' => now(),
                    'product_id' => $productId,
                    'warehouse_id' => $warehouseId,
                    'batch_id' => $bb->batch_id,
                    'movement_type' => $movementType,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'quantity' => -$deductQty,
                    'unit_cost' => $unitCost,
                    'balance_after' => $newQty,
                    'notes' => $notes . ($bb->batch ? " (Auto-FIFO Lot {$bb->batch->batch_number})" : ''),
                    'created_by' => $userId ?? auth()->id(),
                ]);

                $remainingQty -= $deductQty;
            }

            // If any quantity remains, deduct from unbatched
            if ($remainingQty > 0) {
                if (!$unbatchedBalance) {
                    $unbatchedBalance = StockBalance::create([
                        'product_id' => $productId,
                        'warehouse_id' => $warehouseId,
                        'batch_id' => null,
                        'quantity' => 0.0000,
                        'reserved_quantity' => 0.0000,
                    ]);
                    $unbatchedBalance = StockBalance::where('id', $unbatchedBalance->id)->lockForUpdate()->first();
                }

                $newQty = (float)$unbatchedBalance->quantity - $remainingQty;
                $unbatchedBalance->quantity = $newQty;
                $unbatchedBalance->save();

                $unitCost = $product ? (float)$product->purchase_cost : 0.0;

                $lastMovement = StockMovement::create([
                    'movement_date' => now(),
                    'product_id' => $productId,
                    'warehouse_id' => $warehouseId,
                    'batch_id' => null,
                    'movement_type' => $movementType,
                    'reference_type' => $referenceType,
                    'reference_id' => $referenceId,
                    'quantity' => -$remainingQty,
                    'unit_cost' => $unitCost,
                    'balance_after' => $newQty,
                    'notes' => $notes,
                    'created_by' => $userId ?? auth()->id(),
                ]);
            }

            return $lastMovement;
        });
    }

    /**
     * Get real-time available stock for an item across warehouse or specific batch.
     */
    public function getAvailableStock(int $productId, ?int $warehouseId = null, ?int $batchId = null): float
    {
        $query = StockBalance::where('product_id', $productId);

        if ($warehouseId !== null) {
            $query->where('warehouse_id', $warehouseId);
        }

        if ($batchId !== null) {
            $query->where('batch_id', $batchId);
        }

        return (float) $query->sum('quantity');
    }

    /**
     * Helper to allocate batches using FEFO (first-expiry, first-out) or FIFO.
     */
    public function allocateBatches(int $productId, int $warehouseId, float $requiredQuantity): array
    {
        $balances = StockBalance::where('product_id', $productId)
            ->where('warehouse_id', $warehouseId)
            ->where('quantity', '>', 0)
            ->with(['batch' => function ($q) {
                $q->orderByRaw('expiry_date IS NULL, expiry_date ASC, id ASC');
            }])
            ->get();

        $allocated = [];
        $remaining = $requiredQuantity;

        foreach ($balances as $b) {
            $avail = (float)$b->quantity;
            if ($avail <= 0) continue;

            $take = min($avail, $remaining);
            $allocated[] = [
                'batch_id' => $b->batch_id,
                'batch_number' => $b->batch ? $b->batch->batch_number : null,
                'quantity' => $take,
                'unit_cost' => $b->batch ? (float)$b->batch->cost_per_unit : 0.0,
            ];

            $remaining -= $take;
            if ($remaining <= 0) {
                break;
            }
        }

        if ($remaining > 0) {
            $product = Product::find($productId);
            $name = $product ? $product->name : "Product #{$productId}";
            throw new InsufficientStockException("Insufficient batch stock for {$name}. Needed: {$requiredQuantity}, Shortfall: {$remaining}.");
        }

        return $allocated;
    }

    /**
     * Post a controlled stock adjustment document.
     */
    public function postAdjustment(StockAdjustment $adjustment): void
    {
        DB::transaction(function () use ($adjustment) {
            $adjustment->load('items');

            foreach ($adjustment->items as $item) {
                if ($item->type === 'add') {
                    $this->addStock(
                        productId: $item->product_id,
                        warehouseId: $adjustment->warehouse_id,
                        quantity: (float)$item->quantity,
                        unitCost: (float)$item->unit_cost,
                        movementType: 'adjustment_add',
                        batchId: $item->batch_id,
                        referenceType: StockAdjustment::class,
                        referenceId: $adjustment->id,
                        notes: "Adjustment: {$adjustment->reason}",
                        userId: $adjustment->created_by
                    );
                } else {
                    $this->deductStock(
                        productId: $item->product_id,
                        warehouseId: $adjustment->warehouse_id,
                        quantity: (float)$item->quantity,
                        movementType: 'adjustment_sub',
                        batchId: $item->batch_id,
                        referenceType: StockAdjustment::class,
                        referenceId: $adjustment->id,
                        notes: "Adjustment: {$adjustment->reason}",
                        userId: $adjustment->created_by
                    );
                }
            }

            $adjustment->status = 'approved';
            $adjustment->approved_by = auth()->id();
            $adjustment->save();
        });
    }
}
