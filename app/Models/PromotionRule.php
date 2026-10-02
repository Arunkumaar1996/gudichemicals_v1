<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionRule extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'buy_quantity' => 'decimal:4',
        'get_quantity' => 'decimal:4',
        'discount_pct' => 'decimal:2',
        'discount_flat' => 'decimal:2',
        'conditions_json' => 'array',
    ];

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class);
    }

    public function buyProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'buy_product_id');
    }

    public function getProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'get_product_id');
    }
}
