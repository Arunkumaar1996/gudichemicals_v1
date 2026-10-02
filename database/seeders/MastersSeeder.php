<?php

namespace Database\Seeders;

use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\ExpenseCategory;
use App\Models\HsnCode;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Models\UnitConversion;
use App\Models\Vendor;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class MastersSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Company Setting
        CompanySetting::current();

        // 2. Warehouses
        $whMain = Warehouse::firstOrCreate(
            ['code' => 'WH-MAIN'],
            ['name' => 'Main Chemical Plant & Raw Stores', 'location' => 'Plot 42, MIDC Chemical Zone, Pune', 'is_primary' => true, 'is_active' => true]
        );
        $whFg = Warehouse::firstOrCreate(
            ['code' => 'WH-FG'],
            ['name' => 'Finished Goods & Distribution Warehouse', 'location' => 'Bay 3, MIDC Logistics Hub, Pune', 'is_primary' => false, 'is_active' => true]
        );

        // 3. Units
        $ltr = Unit::firstOrCreate(['code' => 'LTR'], ['name' => 'Litre', 'is_fractional' => true]);
        $kg  = Unit::firstOrCreate(['code' => 'KG'],  ['name' => 'Kilogram', 'is_fractional' => true]);
        $ml  = Unit::firstOrCreate(['code' => 'ML'],  ['name' => 'Millilitre', 'is_fractional' => true]);
        $gm  = Unit::firstOrCreate(['code' => 'GM'],  ['name' => 'Gram', 'is_fractional' => true]);
        $nos = Unit::firstOrCreate(['code' => 'NOS'], ['name' => 'Numbers / Pieces', 'is_fractional' => false]);
        $btl = Unit::firstOrCreate(['code' => 'BTL'], ['name' => 'Bottle', 'is_fractional' => false]);
        $can = Unit::firstOrCreate(['code' => 'CAN'], ['name' => 'Can (5L/20L)', 'is_fractional' => false]);
        $drum = Unit::firstOrCreate(['code' => 'DRUM'], ['name' => 'Drum (200L)', 'is_fractional' => false]);

        // 4. Unit Conversions
        UnitConversion::firstOrCreate(['from_unit_id' => $ltr->id, 'to_unit_id' => $ml->id], ['conversion_factor' => 1000]);
        UnitConversion::firstOrCreate(['from_unit_id' => $kg->id, 'to_unit_id' => $gm->id], ['conversion_factor' => 1000]);
        UnitConversion::firstOrCreate(['from_unit_id' => $drum->id, 'to_unit_id' => $ltr->id], ['conversion_factor' => 200]);
        UnitConversion::firstOrCreate(['from_unit_id' => $can->id, 'to_unit_id' => $ltr->id], ['conversion_factor' => 5]);

        // 5. Product Categories
        $catRaw = ProductCategory::firstOrCreate(['slug' => 'raw-chemicals'], ['name' => 'Raw Chemicals', 'description' => 'Acids, bases, surfactants and industrial raw chemicals']);
        $catSemi = ProductCategory::firstOrCreate(['slug' => 'semi-finished-liquid'], ['name' => 'Semi-Finished Intermediates', 'description' => 'Bulk compounded liquid solutions']);
        $catCleaners = ProductCategory::firstOrCreate(['slug' => 'industrial-cleaners'], ['name' => 'Industrial Cleaners & Degreasers', 'description' => 'Packaged finished degreasers and detergents']);
        $catPack = ProductCategory::firstOrCreate(['slug' => 'packaging-materials'], ['name' => 'Packaging Materials', 'description' => 'HDPE bottles, jerry cans, caps, seals, and cartons']);

        // 6. HSN Codes
        HsnCode::firstOrCreate(['code' => '2807'], ['description' => 'Sulphuric acid; oleum', 'gst_rate' => 18.00]);
        HsnCode::firstOrCreate(['code' => '2806'], ['description' => 'Hydrogen chloride / DM Water chemical additives', 'gst_rate' => 18.00]);
        HsnCode::firstOrCreate(['code' => '3402'], ['description' => 'Organic surface-active agents; cleaning preparations', 'gst_rate' => 18.00]);
        HsnCode::firstOrCreate(['code' => '3923'], ['description' => 'Articles for conveyance or packing of goods, of plastics', 'gst_rate' => 18.00]);

        // 7. Expense Categories
        ExpenseCategory::firstOrCreate(['name' => 'Electricity & Utilities'], ['description' => 'Factory power, water connection, plant bills']);
        ExpenseCategory::firstOrCreate(['name' => 'Factory Rent & Lease'], ['description' => 'Premises lease']);
        ExpenseCategory::firstOrCreate(['name' => 'Freight & Logistics'], ['description' => 'Inward raw chemical tanker freight & dispatch']);
        ExpenseCategory::firstOrCreate(['name' => 'Maintenance & Repairs'], ['description' => 'Mixing tank, pump and machinery servicing']);
        ExpenseCategory::firstOrCreate(['name' => 'Wages & Plant Labor'], ['description' => 'Production worker and operator stipends']);

        // 8. Vendors
        Vendor::firstOrCreate(
            ['phone' => '9822001122'],
            [
                'name' => 'Gujarat Alkalies & Chemicals Ltd',
                'company_name' => 'GACL Vadodara',
                'gstin' => '24AAACG1234D1Z2',
                'pan' => 'AAACG1234D',
                'email' => 'sales@gacl.com',
                'address' => 'P.O. Petrochemicals, Vadodara, Gujarat',
                'state_code' => '24',
                'state_name' => 'Gujarat',
                'credit_period_days' => 30,
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
            ]
        );

        Vendor::firstOrCreate(
            ['phone' => '9822003344'],
            [
                'name' => 'Supreme Polymers & Packaging',
                'company_name' => 'Supreme Bottles MIDC',
                'gstin' => '27AAACS5678E1Z1',
                'pan' => 'AAACS5678E',
                'email' => 'orders@supremepackaging.in',
                'address' => 'Plot 88, Bhosari MIDC, Pune',
                'state_code' => '27',
                'state_name' => 'Maharashtra',
                'credit_period_days' => 45,
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
            ]
        );

        // 9. Customers
        Customer::walkInCustomer();

        Customer::firstOrCreate(
            ['phone' => '9890123456'],
            [
                'customer_type' => 'wholesale',
                'name' => 'Shiv Industrial Solutions',
                'company_name' => 'Shiv Enterprises',
                'gstin' => '27AAACS9988F1Z9',
                'pan' => 'AAACS9988F',
                'email' => 'purchase@shivindustrials.com',
                'billing_address' => 'Gala 14, Chakan MIDC Phase 2, Pune',
                'shipping_address' => 'Gala 14, Chakan MIDC Phase 2, Pune',
                'city' => 'Pune',
                'state_code' => '27',
                'state_name' => 'Maharashtra',
                'pincode' => '410501',
                'credit_limit' => 200000.00,
                'opening_balance' => 0.00,
                'current_balance' => 0.00,
            ]
        );

        // 10. Sample Products
        // Raw Chemicals
        Product::firstOrCreate(
            ['sku' => 'RAW-SULPH-01'],
            [
                'barcode' => '890100100001',
                'name' => 'Commercial Sulphuric Acid 98%',
                'category_id' => $catRaw->id,
                'item_type' => 'raw_material',
                'unit_id' => $kg->id,
                'hsn_code' => '2807',
                'gst_rate' => 18.00,
                'purchase_cost' => 32.5000,
                'retail_price' => 0.0000,
                'wholesale_price' => 0.0000,
                'reorder_level' => 500.0000,
                'has_batch_tracking' => true,
                'has_expiry_tracking' => false,
                'description' => 'Industrial grade H2SO4 for manufacturing cleaning & pickling formulations',
            ]
        );

        Product::firstOrCreate(
            ['sku' => 'RAW-LABSA-01'],
            [
                'barcode' => '890100100002',
                'name' => 'Linear Alkyl Benzene Sulphonic Acid (LABSA 90%)',
                'category_id' => $catRaw->id,
                'item_type' => 'raw_material',
                'unit_id' => $kg->id,
                'hsn_code' => '3402',
                'gst_rate' => 18.00,
                'purchase_cost' => 115.0000,
                'retail_price' => 0.0000,
                'wholesale_price' => 0.0000,
                'reorder_level' => 300.0000,
                'has_batch_tracking' => true,
                'has_expiry_tracking' => true,
                'description' => 'Anionic surfactant active matter for detergents and degreasers',
            ]
        );

        Product::firstOrCreate(
            ['sku' => 'RAW-DM-WATER'],
            [
                'barcode' => '890100100003',
                'name' => 'Demineralized Process Water (DM Water)',
                'category_id' => $catRaw->id,
                'item_type' => 'raw_material',
                'unit_id' => $ltr->id,
                'hsn_code' => '2806',
                'gst_rate' => 18.00,
                'purchase_cost' => 1.2000,
                'retail_price' => 0.0000,
                'wholesale_price' => 0.0000,
                'reorder_level' => 2000.0000,
                'has_batch_tracking' => false,
                'has_expiry_tracking' => false,
                'description' => 'Low conductivity demineralized water for batch compounding',
            ]
        );

        // Packaging
        Product::firstOrCreate(
            ['sku' => 'PKG-BTL-1L'],
            [
                'barcode' => '890100200001',
                'name' => '1 Litre HDPE Chemical Bottle with Cap & Induction Seal',
                'category_id' => $catPack->id,
                'item_type' => 'packaging',
                'unit_id' => $nos->id,
                'hsn_code' => '3923',
                'gst_rate' => 18.00,
                'purchase_cost' => 11.5000,
                'retail_price' => 0.0000,
                'wholesale_price' => 0.0000,
                'reorder_level' => 1000.0000,
                'has_batch_tracking' => false,
                'description' => 'Opaque chemical resistant HDPE bottle 1L',
            ]
        );

        Product::firstOrCreate(
            ['sku' => 'PKG-CAN-5L'],
            [
                'barcode' => '890100200002',
                'name' => '5 Litre Heavy Duty Chemical Jerry Can',
                'category_id' => $catPack->id,
                'item_type' => 'packaging',
                'unit_id' => $nos->id,
                'hsn_code' => '3923',
                'gst_rate' => 18.00,
                'purchase_cost' => 42.0000,
                'retail_price' => 0.0000,
                'wholesale_price' => 0.0000,
                'reorder_level' => 200.0000,
                'has_batch_tracking' => false,
                'description' => 'UN certified 5L Jerry can for hazardous cleaners',
            ]
        );

        // Finished Goods
        Product::firstOrCreate(
            ['sku' => 'FG-DEGREASE-1L'],
            [
                'barcode' => '890100300001',
                'name' => 'Gudi Ultra Clean Industrial Degreaser 1L',
                'category_id' => $catCleaners->id,
                'item_type' => 'finished_goods',
                'unit_id' => $btl->id,
                'hsn_code' => '3402',
                'gst_rate' => 18.00,
                'purchase_cost' => 65.0000,
                'retail_price' => 180.0000,
                'wholesale_price' => 140.0000,
                'min_selling_price' => 125.0000,
                'reorder_level' => 150.0000,
                'has_batch_tracking' => true,
                'has_expiry_tracking' => true,
                'description' => 'High-power alkaline degreaser for machinery oil, carbon and grease removal',
            ]
        );

        Product::firstOrCreate(
            ['sku' => 'FG-ACID-CLEAN-5L'],
            [
                'barcode' => '890100300002',
                'name' => 'Gudi Heavy Duty Acid Pickling Cleaner 5L',
                'category_id' => $catCleaners->id,
                'item_type' => 'finished_goods',
                'unit_id' => $can->id,
                'hsn_code' => '3402',
                'gst_rate' => 18.00,
                'purchase_cost' => 220.0000,
                'retail_price' => 650.0000,
                'wholesale_price' => 520.0000,
                'min_selling_price' => 480.0000,
                'reorder_level' => 50.0000,
                'has_batch_tracking' => true,
                'has_expiry_tracking' => true,
                'description' => 'Inhibited acid pickling and rust descaling solution in 5L container',
            ]
        );

        // Seed Multiple Batches & Stock for Finished Chemicals
        $invService = app(\App\Services\Inventory\InventoryService::class);
        $prodDegrease = Product::where('sku', 'FG-DEGREASE-1L')->first();
        $prodAcid = Product::where('sku', 'FG-ACID-CLEAN-5L')->first();

        if ($prodDegrease && $whMain) {
            $batchD1 = \App\Models\InventoryBatch::firstOrCreate(
                ['product_id' => $prodDegrease->id, 'batch_number' => 'LOT-DG-2026-01'],
                ['mfg_date' => '2026-08-01', 'expiry_date' => '2027-07-31', 'cost_per_unit' => 65.0, 'is_active' => true]
            );
            $invService->addStock(
                productId: $prodDegrease->id,
                warehouseId: $whMain->id,
                quantity: 45.0,
                unitCost: 65.0,
                movementType: 'opening_stock',
                batchId: $batchD1->id,
                notes: 'Primary production lot 01'
            );

            $batchD2 = \App\Models\InventoryBatch::firstOrCreate(
                ['product_id' => $prodDegrease->id, 'batch_number' => 'LOT-DG-2026-02'],
                ['mfg_date' => '2026-09-15', 'expiry_date' => '2027-09-14', 'cost_per_unit' => 65.0, 'is_active' => true]
            );
            $invService->addStock(
                productId: $prodDegrease->id,
                warehouseId: $whMain->id,
                quantity: 80.0,
                unitCost: 65.0,
                movementType: 'opening_stock',
                batchId: $batchD2->id,
                notes: 'Secondary production lot 02'
            );
        }

        if ($prodAcid && $whMain) {
            $batchA1 = \App\Models\InventoryBatch::firstOrCreate(
                ['product_id' => $prodAcid->id, 'batch_number' => 'LOT-AC-2026-01'],
                ['mfg_date' => '2026-07-10', 'expiry_date' => '2027-07-09', 'cost_per_unit' => 220.0, 'is_active' => true]
            );
            $invService->addStock(
                productId: $prodAcid->id,
                warehouseId: $whMain->id,
                quantity: 30.0,
                unitCost: 220.0,
                movementType: 'opening_stock',
                batchId: $batchA1->id,
                notes: 'Acid cleaner lot 01'
            );

            $batchA2 = \App\Models\InventoryBatch::firstOrCreate(
                ['product_id' => $prodAcid->id, 'batch_number' => 'LOT-AC-2026-02'],
                ['mfg_date' => '2026-09-20', 'expiry_date' => '2027-09-19', 'cost_per_unit' => 220.0, 'is_active' => true]
            );
            $invService->addStock(
                productId: $prodAcid->id,
                warehouseId: $whMain->id,
                quantity: 50.0,
                unitCost: 220.0,
                movementType: 'opening_stock',
                batchId: $batchA2->id,
                notes: 'Acid cleaner lot 02'
            );
        }
    }
}
