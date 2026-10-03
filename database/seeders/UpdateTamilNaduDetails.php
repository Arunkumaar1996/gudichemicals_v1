<?php

namespace Database\Seeders;

use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class UpdateTamilNaduDetails extends Seeder
{
    public function run(): void
    {
        $setting = CompanySetting::first();
        if ($setting) {
            $setting->update([
                'company_name' => 'Gudi Chemicals',
                'trade_name' => 'Gudi Chemicals — Home & Commercial Cleaning Products',
                'address' => 'Spic, Thenkarai',
                'city' => 'Coimbatore',
                'state_code' => '33',
                'state_name' => 'Tamil Nadu',
                'pincode' => '641010',
                'phone' => '9842212345',
                'email' => 'contact@gudichemicals.com',
                'gstin' => '33AAACG1234D1Z5',
                'terms_and_conditions' => 'Gudi Chemicals is a trusted manufacturer and supplier of high-quality home cleaning products in Coimbatore, Tamil Nadu (Floor Cleaner, Toilet Cleaner, Glass Cleaner, Dish Wash, Hand Wash, Liquid Detergent, Fabric Conditioner). Goods once sold will be accepted for return only in original sealed packaging.',
            ]);
        }

        $walkIn = Customer::where('phone', '0000000000')->first();
        if ($walkIn) {
            $walkIn->update([
                'billing_address' => 'Spic, Thenkarai',
                'shipping_address' => 'Spic, Thenkarai',
                'city' => 'Coimbatore',
                'state_code' => '33',
                'state_name' => 'Tamil Nadu',
                'pincode' => '641010',
            ]);
        }

        Customer::updateOrCreate(
            ['phone' => '9842299881'],
            [
                'name' => 'Gudi Wholesale & Dealership Depot',
                'company_name' => 'Gudi Cleaning Supplies Dealership',
                'customer_type' => 'wholesale',
                'billing_address' => 'Spic, Thenkarai',
                'shipping_address' => 'Spic, Thenkarai',
                'city' => 'Coimbatore',
                'state_code' => '33',
                'state_name' => 'Tamil Nadu',
                'pincode' => '641010',
                'credit_limit' => 150000.00,
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
                'is_active' => true,
            ]
        );

        $wh = Warehouse::where('code', 'WH-MAIN')->first();
        if ($wh) {
            $wh->update([
                'name' => 'Main Chemical Depot & Stores',
                'location' => 'Spic, Thenkarai, Coimbatore, Tamil Nadu 641010',
            ]);
        }

        $whFg = Warehouse::where('code', 'WH-FG')->first();
        if ($whFg) {
            $whFg->update([
                'name' => 'Finished Cleaning Products Warehouse',
                'location' => 'Spic, Thenkarai, Coimbatore, Tamil Nadu 641010',
            ]);
        }
    }
}
