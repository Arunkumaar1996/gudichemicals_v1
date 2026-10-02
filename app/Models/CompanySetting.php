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
            'trade_name' => 'Gudi Chemicals & Industrial Solutions',
            'gstin' => '27AAACG1234D1Z5',
            'state_code' => '27',
            'state_name' => 'Maharashtra',
            'currency_symbol' => '₹',
            'invoice_prefix' => 'GC',
            'fy_code' => '2026-27',
        ]);
    }
}
