<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Formula;
use App\Models\FormulaItem;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\SalesInvoice;
use App\Models\Unit;
use App\Models\User;
use App\Models\Vendor;
use App\Models\Warehouse;
use App\Services\Inventory\InventoryService;
use Database\Seeders\MastersSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ErpUiAndEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->seed(MastersSeeder::class);

        $this->admin = User::where('email', 'admin@gudichemicals.com')->firstOrFail();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@gudichemicals.com',
            'password' => 'Admin@12345',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_dashboard_renders_successfully_for_authenticated_user(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Gudi Chemicals — Operations Dashboard');
    }

    public function test_pos_desk_and_ajax_calculation(): void
    {
        $customer = Customer::walkInCustomer();
        $product = Product::where('sku', 'FG-DEGREASE-1L')->firstOrFail();

        // 1. Visit POS
        $response = $this->actingAs($this->admin)->get('/pos');
        $response->assertStatus(200);
        $response->assertSee('POS Fast Billing');

        // 2. AJAX Calculation for 2 units
        $calcResponse = $this->actingAs($this->admin)->postJson('/pos/calculate', [
            'customer_id' => $customer->id,
            'price_tier' => 'retail',
            'cart_items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2,
                ]
            ]
        ]);

        $calcResponse->assertStatus(200);
        $calcResponse->assertJsonStructure([
            'is_interstate',
            'taxable_amount',
            'cgst_amount',
            'sgst_amount',
            'grand_total',
            'items',
        ]);

        // Retail price ₹180 * 2 = ₹360 + 18% GST (₹64.80) = ₹425
        $this->assertEquals(425.0, (float)$calcResponse->json('grand_total'));
    }

    public function test_pos_store_creates_invoice_and_deducts_stock(): void
    {
        $customer = Customer::walkInCustomer();
        $product = Product::where('sku', 'FG-DEGREASE-1L')->firstOrFail();
        $warehouse = Warehouse::where('code', 'WH-MAIN')->firstOrFail();
        $invService = app(InventoryService::class);

        // Pre-fill stock
        $invService->addStock($product->id, $warehouse->id, 20.0, 65.0, 'opening_stock');

        $response = $this->actingAs($this->admin)->postJson('/pos/store', [
            'customer_id' => $customer->id,
            'warehouse_id' => $warehouse->id,
            'price_tier' => 'retail',
            'cart_items' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3,
                ]
            ],
            'payments' => [
                [
                    'amount' => 637.00,
                    'payment_method' => 'cash',
                ]
            ],
            'notes' => 'Counter cash sale'
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Verify stock deducted by 3: 20 - 3 = 17
        $this->assertEquals(17.0, $invService->getAvailableStock($product->id, $warehouse->id));

        // Verify invoice was created in database
        $this->assertDatabaseHas('sales_invoices', [
            'customer_id' => $customer->id,
            'status' => 'posted',
            'payment_status' => 'paid',
        ]);
    }

    public function test_purchase_order_creation_and_approval(): void
    {
        $vendor = Vendor::firstOrFail();
        $warehouse = Warehouse::firstOrFail();
        $product = Product::where('sku', 'RAW-SULPH-01')->firstOrFail();
        $unit = Unit::where('code', 'KG')->firstOrFail();

        $response = $this->actingAs($this->admin)->post('/purchases/orders', [
            'vendor_id' => $vendor->id,
            'warehouse_id' => $warehouse->id,
            'order_date' => now()->toDateString(),
            'items' => [
                [
                    'product_id' => $product->id,
                    'unit_id' => $unit->id,
                    'quantity' => 500,
                    'unit_price' => 32.50,
                    'tax_rate' => 18.00,
                ]
            ]
        ]);

        $response->assertRedirect();
        $po = PurchaseOrder::latest()->first();
        $this->assertNotNull($po);
        $this->assertEquals('draft', $po->status);

        // Approve PO
        $approveResponse = $this->actingAs($this->admin)->post("/purchases/orders/{$po->id}/approve");
        $approveResponse->assertRedirect();
        $this->assertEquals('approved', $po->fresh()->status);
    }

    public function test_goods_receipt_adds_stock_and_creates_batch(): void
    {
        $vendor = Vendor::firstOrFail();
        $warehouse = Warehouse::firstOrFail();
        $product = Product::where('sku', 'RAW-SULPH-01')->firstOrFail();
        $invService = app(InventoryService::class);

        $initialStock = $invService->getAvailableStock($product->id, $warehouse->id);

        $response = $this->actingAs($this->admin)->post('/purchases/grn', [
            'vendor_id' => $vendor->id,
            'warehouse_id' => $warehouse->id,
            'receipt_date' => now()->toDateString(),
            'supplier_challan_no' => 'CH-8899',
            'supplier_invoice_no' => 'INV-9901',
            'items' => [
                [
                    'product_id' => $product->id,
                    'batch_number' => 'LOT-SULPH-2026-X',
                    'supplier_lot_number' => 'GACL-LOT-11',
                    'quantity' => 300,
                    'unit_cost' => 32.50,
                    'tax_rate' => 18.00,
                ]
            ]
        ]);

        $response->assertRedirect();

        // Stock must have increased by 300
        $this->assertEquals($initialStock + 300, $invService->getAvailableStock($product->id, $warehouse->id));

        // Batch record must exist
        $this->assertDatabaseHas('inventory_batches', [
            'product_id' => $product->id,
            'batch_number' => 'LOT-SULPH-2026-X',
        ]);
    }

    public function test_reports_pages_render_without_errors(): void
    {
        $salesReport = $this->actingAs($this->admin)->get('/reports/sales');
        $salesReport->assertStatus(200);
        $salesReport->assertSee('Sales Register');

        $inventoryReport = $this->actingAs($this->admin)->get('/reports/inventory');
        $inventoryReport->assertStatus(200);
        $inventoryReport->assertSee('Inventory Valuation Report');

        $productionReport = $this->actingAs($this->admin)->get('/reports/production');
        $productionReport->assertStatus(200);
        $productionReport->assertSee('Production Yield & Costing Report');
    }
}
