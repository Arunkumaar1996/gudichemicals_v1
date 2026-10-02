<?php

namespace Tests\Feature;

use App\Exceptions\InsufficientStockException;
use App\Models\Customer;
use App\Models\Formula;
use App\Models\FormulaItem;
use App\Models\Product;
use App\Models\Unit;
use App\Models\Warehouse;
use App\Services\Inventory\InventoryService;
use App\Services\Production\ProductionService;
use App\Services\Purchasing\PurchasingService;
use App\Services\Sales\InvoicePostingService;
use App\Services\Tax\GstCalculationService;
use Database\Seeders\MastersSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErpCoreServicesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(MastersSeeder::class);
    }

    public function test_inventory_add_and_deduct_with_insufficient_stock_guard(): void
    {
        $inventoryService = app(InventoryService::class);
        $product = Product::where('sku', 'RAW-SULPH-01')->firstOrFail();
        $warehouse = Warehouse::where('code', 'WH-MAIN')->firstOrFail();

        // 1. Initially stock is 0
        $this->assertEquals(0, $inventoryService->getAvailableStock($product->id, $warehouse->id));

        // 2. Add 100 kg stock
        $inventoryService->addStock(
            productId: $product->id,
            warehouseId: $warehouse->id,
            quantity: 100.0,
            unitCost: 35.0,
            movementType: 'purchase_receipt',
            notes: 'Test batch delivery'
        );

        $this->assertEquals(100.0, $inventoryService->getAvailableStock($product->id, $warehouse->id));

        // 3. Deduct 40 kg stock
        $inventoryService->deductStock(
            productId: $product->id,
            warehouseId: $warehouse->id,
            quantity: 40.0,
            movementType: 'production_consumption'
        );

        $this->assertEquals(60.0, $inventoryService->getAvailableStock($product->id, $warehouse->id));

        // 4. Attempt to deduct 70 kg (exceeds 60 kg) -> Expect InsufficientStockException
        $this->expectException(InsufficientStockException::class);
        $inventoryService->deductStock(
            productId: $product->id,
            warehouseId: $warehouse->id,
            quantity: 70.0,
            movementType: 'production_consumption'
        );
    }

    public function test_gst_calculation_intra_vs_inter_state(): void
    {
        $gstService = app(GstCalculationService::class);

        // Intra-state (Maharashtra 27 to Maharashtra 27): 18% GST -> 9% CGST + 9% SGST
        $intra = $gstService->calculateLineItem(
            unitPrice: 100.0,
            quantity: 2.0,
            discountAmount: 0.0,
            gstRate: 18.0,
            isInterstate: false
        );

        $this->assertEquals(200.0, $intra['taxable_amount']);
        $this->assertEquals(18.0, $intra['cgst_amount']);
        $this->assertEquals(18.0, $intra['sgst_amount']);
        $this->assertEquals(0.0, $intra['igst_amount']);
        $this->assertEquals(36.0, $intra['tax_amount']);
        $this->assertEquals(236.0, $intra['line_total']);

        // Inter-state (Maharashtra 27 to Gujarat 24): 18% GST -> 18% IGST
        $inter = $gstService->calculateLineItem(
            unitPrice: 100.0,
            quantity: 2.0,
            discountAmount: 0.0,
            gstRate: 18.0,
            isInterstate: true
        );

        $this->assertEquals(200.0, $inter['taxable_amount']);
        $this->assertEquals(0.0, $inter['cgst_amount']);
        $this->assertEquals(0.0, $inter['sgst_amount']);
        $this->assertEquals(36.0, $inter['igst_amount']);
        $this->assertEquals(236.0, $inter['line_total']);
    }

    public function test_sales_invoice_posting_and_automatic_stock_deduction(): void
    {
        $inventoryService = app(InventoryService::class);
        $invoiceService = app(InvoicePostingService::class);

        $product = Product::where('sku', 'FG-DEGREASE-1L')->firstOrFail();
        $warehouse = Warehouse::where('code', 'WH-MAIN')->firstOrFail();
        $customer = Customer::walkInCustomer();

        $initialStock = $inventoryService->getAvailableStock($product->id, $warehouse->id);

        // Add 50 bottles of finished product to inventory
        $inventoryService->addStock(
            productId: $product->id,
            warehouseId: $warehouse->id,
            quantity: 50.0,
            unitCost: 65.0,
            movementType: 'opening_stock'
        );

        $this->assertEquals($initialStock + 50.0, $inventoryService->getAvailableStock($product->id, $warehouse->id));

        // Post an invoice for 5 bottles at Retail price (₹180 * 5 = ₹900 + 18% GST = ₹1062)
        $invoice = $invoiceService->postInvoice(
            rawCartItems: [
                [
                    'product_id' => $product->id,
                    'quantity' => 5,
                    'discount_amount' => 0,
                ]
            ],
            customerId: $customer->id,
            warehouseId: $warehouse->id,
            priceTier: 'retail',
            payments: [
                [
                    'amount' => 1062.00,
                    'payment_method' => 'cash',
                ]
            ]
        );

        $this->assertNotNull($invoice);
        $this->assertStringStartsWith('GC/2026-27/', $invoice->invoice_number);
        $this->assertEquals(1062.00, (float)$invoice->grand_total);
        $this->assertEquals('paid', $invoice->payment_status);

        // Verify stock reduced by 5
        $this->assertEquals($initialStock + 50.0 - 5.0, $inventoryService->getAvailableStock($product->id, $warehouse->id));
    }

    public function test_chemical_production_order_scaling_and_batch_finalization(): void
    {
        $inventoryService = app(InventoryService::class);
        $productionService = app(ProductionService::class);

        $rawWater = Product::where('sku', 'RAW-DM-WATER')->firstOrFail();
        $rawSulph = Product::where('sku', 'RAW-SULPH-01')->firstOrFail();
        $fgAcid = Product::where('sku', 'FG-ACID-CLEAN-5L')->firstOrFail();
        $pkgCan = Product::where('sku', 'PKG-CAN-5L')->firstOrFail();
        $warehouse = Warehouse::where('code', 'WH-MAIN')->firstOrFail();
        $unitCan = Unit::where('code', 'CAN')->firstOrFail();
        $unitKg = Unit::where('code', 'KG')->firstOrFail();
        $unitLtr = Unit::where('code', 'LTR')->firstOrFail();
        $unitNos = Unit::where('code', 'NOS')->firstOrFail();

        // 1. Create Formula: Standard Batch 10 Cans (50 Litres)
        // Consumes: 40L Water, 15Kg Sulphuric Acid, 10 Packaging Cans
        $formula = Formula::create([
            'formula_code' => 'FORM-ACID-01',
            'name' => 'Standard Heavy Acid Descaler 5L Compound',
            'product_id' => $fgAcid->id,
            'version' => 1,
            'standard_batch_qty' => 10.0,
            'output_unit_id' => $unitCan->id,
            'expected_yield_pct' => 100.0,
            'is_approved' => true,
            'is_active' => true,
        ]);

        FormulaItem::create([
            'formula_id' => $formula->id,
            'ingredient_product_id' => $rawWater->id,
            'quantity' => 40.0,
            'unit_id' => $unitLtr->id,
        ]);
        FormulaItem::create([
            'formula_id' => $formula->id,
            'ingredient_product_id' => $rawSulph->id,
            'quantity' => 15.0,
            'unit_id' => $unitKg->id,
        ]);

        // 2. Add Raw Material & Packaging Stock
        $inventoryService->addStock($rawWater->id, $warehouse->id, 500.0, 1.20, 'purchase_receipt');
        $inventoryService->addStock($rawSulph->id, $warehouse->id, 200.0, 35.00, 'purchase_receipt');
        $inventoryService->addStock($pkgCan->id, $warehouse->id, 100.0, 42.00, 'purchase_receipt');

        // 3. Create Production Order planned for 20 Cans (2x standard batch)
        $order = $productionService->createOrder(
            formulaId: $formula->id,
            plannedQty: 20.0,
            targetWarehouseId: $warehouse->id,
            orderDate: now()->toDateString()
        );

        $this->assertEquals(20.0, (float)$order->planned_qty);
        $order->load('consumptions');
        // Scaled requirements: 40 * 2 = 80L Water, 15 * 2 = 30Kg Sulphuric Acid
        $waterReq = $order->consumptions->where('product_id', $rawWater->id)->first();
        $sulphReq = $order->consumptions->where('product_id', $rawSulph->id)->first();
        $this->assertEquals(80.0, (float)$waterReq->planned_qty);
        $this->assertEquals(30.0, (float)$sulphReq->planned_qty);

        // 4. Finalize batch producing 20 Cans
        $actualConsumptions = [
            ['consumption_id' => $waterReq->id, 'actual_consumed_qty' => 80.0],
            ['consumption_id' => $sulphReq->id, 'actual_consumed_qty' => 30.0],
        ];

        $initialFgStock = $inventoryService->getAvailableStock($fgAcid->id, $warehouse->id);

        $finalizedOrder = $productionService->finalizeBatch(
            order: $order,
            actualYieldQty: 20.0,
            actualConsumptions: $actualConsumptions,
            packagingCost: 840.0, // 20 cans * ₹42
            overheadCost: 200.0
        );

        $this->assertEquals('completed', $finalizedOrder->status);
        $this->assertEquals(20.0, (float)$finalizedOrder->actual_qty);

        // 5. Verify stock deduction:
        // Water: 500 - 80 = 420
        $this->assertEquals(420.0, $inventoryService->getAvailableStock($rawWater->id, $warehouse->id));
        // Sulph: 200 - 30 = 170
        $this->assertEquals(170.0, $inventoryService->getAvailableStock($rawSulph->id, $warehouse->id));
        // Finished Goods: initial + 20
        $this->assertEquals($initialFgStock + 20.0, $inventoryService->getAvailableStock($fgAcid->id, $warehouse->id));
    }
}
