<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductionOrder extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'order_date' => 'date',
        'start_date' => 'datetime',
        'completion_date' => 'datetime',
        'planned_qty' => 'decimal:4',
        'actual_qty' => 'decimal:4',
        'total_material_cost' => 'decimal:4',
        'packaging_cost' => 'decimal:4',
        'overhead_cost' => 'decimal:4',
        'unit_production_cost' => 'decimal:4',
        'formula_snapshot' => 'array',
    ];

    public function formula(): BelongsTo
    {
        return $this->belongsTo(Formula::class);
    }

    public function outputProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'output_product_id');
    }

    public function targetWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'target_warehouse_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(InventoryBatch::class, 'batch_id');
    }

    public function consumptions(): HasMany
    {
        return $this->hasMany(ProductionConsumption::class);
    }

    public function qualityChecks(): HasMany
    {
        return $this->hasMany(ProductionQualityCheck::class);
    }

    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
