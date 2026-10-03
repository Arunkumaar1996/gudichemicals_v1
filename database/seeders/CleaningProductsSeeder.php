<?php

namespace Database\Seeders;

use App\Models\InventoryBatch;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\StockBalance;
use App\Models\StockMovement;
use App\Models\Unit;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CleaningProductsSeeder extends Seeder
{
    public function run(): void
    {
        $whMain = Warehouse::firstOrCreate(
            ['code' => 'WH-MAIN'],
            ['name' => 'Main Chemical Depot & Stores', 'location' => 'Spic, Thenkarai, Coimbatore, Tamil Nadu 641010', 'is_primary' => true, 'is_active' => true]
        );

        $btl = Unit::firstOrCreate(['code' => 'BTL'], ['name' => 'Bottle', 'is_fractional' => false]);
        $nos = Unit::firstOrCreate(['code' => 'NOS'], ['name' => 'Numbers / Pieces', 'is_fractional' => false]);

        $catCleaning = ProductCategory::firstOrCreate(
            ['slug' => 'home-cleaning-products'],
            ['name' => 'Home & Commercial Cleaning Products', 'description' => 'Gudi Chemicals household and commercial cleaning formulations']
        );

        // 8 Products from Gudi Chemicals Instagram & Official Product Line
        $cleaningProducts = [
            [
                'sku' => 'FG-DISHWASH-GEL',
                'barcode' => '890100300010',
                'name' => 'GUDI Dish Wash Power Gel 500ml',
                'category_id' => $catCleaning->id,
                'item_type' => 'finished_goods',
                'unit_id' => $btl->id,
                'hsn_code' => '3402',
                'gst_rate' => 18.00,
                'purchase_cost' => 15.0000,
                'retail_price' => 35.0000,
                'wholesale_price' => 25.0000, // Featured wholesale price ₹25
                'min_selling_price' => 24.0000,
                'reorder_level' => 50.0000,
                'has_batch_tracking' => true,
                'has_expiry_tracking' => true,
                'is_active' => true,
                'description' => 'GUDI Dish Wash Power Gel 500ml - Fresh lemon fragrance, thick power gel formula, tough on grease, gentle on hands. Wholesale ₹25 Only.',
            ],
            [
                'sku' => 'FG-DISHWASH-LIQ',
                'barcode' => '890100300011',
                'name' => 'GUDI Dish Wash Liquid 500ml',
                'category_id' => $catCleaning->id,
                'item_type' => 'finished_goods',
                'unit_id' => $btl->id,
                'hsn_code' => '3402',
                'gst_rate' => 18.00,
                'purchase_cost' => 18.0000,
                'retail_price' => 45.0000,
                'wholesale_price' => 30.0000,
                'min_selling_price' => 28.0000,
                'reorder_level' => 50.0000,
                'has_batch_tracking' => true,
                'has_expiry_tracking' => true,
                'is_active' => true,
                'description' => 'GUDI Dish Wash Liquid 500ml - Active lemon grease removal liquid for all utensils and glassware.',
            ],
            [
                'sku' => 'FG-FLOOR-CLEAN-1L',
                'barcode' => '890100300012',
                'name' => 'GUDI Pine & Citrus Floor Cleaner 1L',
                'category_id' => $catCleaning->id,
                'item_type' => 'finished_goods',
                'unit_id' => $btl->id,
                'hsn_code' => '3402',
                'gst_rate' => 18.00,
                'purchase_cost' => 35.0000,
                'retail_price' => 85.0000,
                'wholesale_price' => 65.0000,
                'min_selling_price' => 60.0000,
                'reorder_level' => 50.0000,
                'has_batch_tracking' => true,
                'has_expiry_tracking' => true,
                'is_active' => true,
                'description' => 'GUDI Pine & Citrus Floor Cleaner 1L - Disinfectant surface cleaner for marble, tile, and stone floors.',
            ],
            [
                'sku' => 'FG-TOILET-CLEAN-500ML',
                'barcode' => '890100300013',
                'name' => 'GUDI Power Toilet Cleaner 500ml',
                'category_id' => $catCleaning->id,
                'item_type' => 'finished_goods',
                'unit_id' => $btl->id,
                'hsn_code' => '3402',
                'gst_rate' => 18.00,
                'purchase_cost' => 25.0000,
                'retail_price' => 65.0000,
                'wholesale_price' => 45.0000,
                'min_selling_price' => 40.0000,
                'reorder_level' => 50.0000,
                'has_batch_tracking' => true,
                'has_expiry_tracking' => true,
                'is_active' => true,
                'description' => 'GUDI Power Toilet Cleaner 500ml - Thick descaling formula kills 99.9% germs and eliminates tough stains.',
            ],
            [
                'sku' => 'FG-GLASS-CLEAN-500ML',
                'barcode' => '890100300014',
                'name' => 'GUDI Sparkle Glass Cleaner 500ml',
                'category_id' => $catCleaning->id,
                'item_type' => 'finished_goods',
                'unit_id' => $btl->id,
                'hsn_code' => '3402',
                'gst_rate' => 18.00,
                'purchase_cost' => 22.0000,
                'retail_price' => 60.0000,
                'wholesale_price' => 40.0000,
                'min_selling_price' => 38.0000,
                'reorder_level' => 50.0000,
                'has_batch_tracking' => true,
                'has_expiry_tracking' => true,
                'is_active' => true,
                'description' => 'GUDI Sparkle Glass Cleaner 500ml - Streak-free glass cleaner for mirrors, car windshields and display glasses.',
            ],
            [
                'sku' => 'FG-HANDWASH-250ML',
                'barcode' => '890100300015',
                'name' => 'GUDI Herbal Gentle Hand Wash 250ml',
                'category_id' => $catCleaning->id,
                'item_type' => 'finished_goods',
                'unit_id' => $btl->id,
                'hsn_code' => '3402',
                'gst_rate' => 18.00,
                'purchase_cost' => 18.0000,
                'retail_price' => 50.0000,
                'wholesale_price' => 35.0000,
                'min_selling_price' => 32.0000,
                'reorder_level' => 50.0000,
                'has_batch_tracking' => true,
                'has_expiry_tracking' => true,
                'is_active' => true,
                'description' => 'GUDI Herbal Gentle Hand Wash 250ml - Antibacterial formula with soft moisturizer and refreshing aroma.',
            ],
            [
                'sku' => 'FG-LIQ-DET-1L',
                'barcode' => '890100300016',
                'name' => 'GUDI Front & Top Load Liquid Detergent 1L',
                'category_id' => $catCleaning->id,
                'item_type' => 'finished_goods',
                'unit_id' => $btl->id,
                'hsn_code' => '3402',
                'gst_rate' => 18.00,
                'purchase_cost' => 45.0000,
                'retail_price' => 120.0000,
                'wholesale_price' => 85.0000,
                'min_selling_price' => 80.0000,
                'reorder_level' => 50.0000,
                'has_batch_tracking' => true,
                'has_expiry_tracking' => true,
                'is_active' => true,
                'description' => 'GUDI Liquid Detergent 1L - Powerful enzyme cleaning for automatic washing machines and delicate clothes.',
            ],
            [
                'sku' => 'FG-FABRIC-COND-1L',
                'barcode' => '890100300017',
                'name' => 'GUDI Soft Touch Fabric Conditioner 1L',
                'category_id' => $catCleaning->id,
                'item_type' => 'finished_goods',
                'unit_id' => $btl->id,
                'hsn_code' => '3402',
                'gst_rate' => 18.00,
                'purchase_cost' => 48.0000,
                'retail_price' => 130.0000,
                'wholesale_price' => 90.0000,
                'min_selling_price' => 85.0000,
                'reorder_level' => 50.0000,
                'has_batch_tracking' => true,
                'has_expiry_tracking' => true,
                'is_active' => true,
                'description' => 'GUDI Soft Touch Fabric Conditioner 1L - Keeps clothes soft, wrinkle-free with lingering floral fragrance.',
            ],
        ];

        foreach ($cleaningProducts as $pData) {
            Product::updateOrCreate(['sku' => $pData['sku']], $pData);
        }

        // Now, update stock quantity to 10 for ALL products in the database
        // As requested: "this apge all product name add and update stack qty in 10 all product add."
        $allProducts = Product::all();

        foreach ($allProducts as $product) {
            // Find or create primary batch for this product
            $batchNumber = 'LOT-' . strtoupper(substr(str_replace(['FG-', 'RAW-', 'PKG-'], '', $product->sku), 0, 8)) . '-2026';
            $batch = InventoryBatch::firstOrCreate(
                [
                    'product_id' => $product->id,
                    'batch_number' => $batchNumber,
                ],
                [
                    'mfg_date' => '2026-09-01',
                    'expiry_date' => '2027-08-31',
                    'cost_per_unit' => $product->purchase_cost ?: 25.00,
                    'is_active' => true,
                ]
            );

            // Delete all existing stock balances for this product so exactly 10.0 remains
            StockBalance::where('product_id', $product->id)->delete();

            // Set the stock balance in WH-MAIN to exactly 10.0000
            StockBalance::create([
                'product_id' => $product->id,
                'warehouse_id' => $whMain->id,
                'batch_id' => $batch->id,
                'quantity' => 10.0000,
                'reserved_quantity' => 0.0000,
            ]);

            // Record stock movement for audit ledger
            StockMovement::create([
                'movement_date' => now(),
                'product_id' => $product->id,
                'warehouse_id' => $whMain->id,
                'batch_id' => $batch->id,
                'movement_type' => 'opening_stock',
                'reference_type' => 'initial_stock_setup',
                'reference_id' => $product->id,
                'quantity' => 10.0000,
                'unit_cost' => $product->purchase_cost ?: 25.00,
                'balance_after' => 10.0000,
                'notes' => 'Stock quantity set to 10 for official Gudi Chemicals catalog update',
                'created_by' => 1,
            ]);
        }
    }
}
