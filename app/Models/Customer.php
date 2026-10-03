<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function salesInvoices(): HasMany
    {
        return $this->hasMany(SalesInvoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(SalesPayment::class);
    }

    public function returns(): HasMany
    {
        return $this->hasMany(SalesReturn::class);
    }

    public static function walkInCustomer(): self
    {
        return static::firstOrCreate(
            ['phone' => '0000000000'],
            [
                'name' => 'Walk-in Customer / Cash Counter',
                'customer_type' => 'retail',
                'billing_address' => 'Spic, Thenkarai',
                'shipping_address' => 'Spic, Thenkarai',
                'city' => 'Coimbatore',
                'state_code' => '33',
                'state_name' => 'Tamil Nadu',
                'pincode' => '641010',
                'is_active' => true,
            ]
        );
    }
}
