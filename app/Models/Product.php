<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'gst_rate' => 'decimal:2',
        'purchase_cost' => 'decimal:4',
        'retail_price' => 'decimal:4',
        'wholesale_price' => 'decimal:4',
        'min_selling_price' => 'decimal:4',
        'reorder_level' => 'decimal:4',
        'has_batch_tracking' => 'boolean',
        'has_expiry_tracking' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function batches(): HasMany
    {
        return $this->hasMany(InventoryBatch::class);
    }

    public function stockBalances(): HasMany
    {
        return $this->hasMany(StockBalance::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function formulas(): HasMany
    {
        return $this->hasMany(Formula::class);
    }

    public function getTotalStockAttribute(): float
    {
        return (float) $this->stockBalances()->sum('quantity');
    }

    public function isLowStock(): bool
    {
        return $this->total_stock <= (float) $this->reorder_level;
    }
}
