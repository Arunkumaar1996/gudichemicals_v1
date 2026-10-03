<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanySetting extends Model
{
    protected $guarded = ['id'];

    public static function current(): self
    {
        return static::firstOrCreate([], [
            'company_name' => 'Gudi Chemicals',
            'trade_name' => 'Gudi Chemicals — Home & Commercial Cleaning Products',
            'gstin' => '33AAACG1234D1Z5',
            'address' => 'Spic, Thenkarai',
            'city' => 'Coimbatore',
            'state_code' => '33',
            'state_name' => 'Tamil Nadu',
            'pincode' => '641010',
            'phone' => '9842212345',
            'email' => 'contact@gudichemicals.com',
            'currency_symbol' => '₹',
            'invoice_prefix' => 'GC',
            'fy_code' => '2026-27',
            'terms_and_conditions' => 'Gudi Chemicals is a trusted manufacturer and supplier of high-quality home cleaning products in Coimbatore, Tamil Nadu. Goods once sold will be accepted for return only in original sealed packaging.',
        ]);
    }
}
