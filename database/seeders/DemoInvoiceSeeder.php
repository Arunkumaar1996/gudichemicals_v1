<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Warehouse;
use App\Services\Inventory\InventoryService;
use App\Services\Production\ProductionService;
use App\Services\Sales\InvoicePostingService;
use Illuminate\Database\Seeder;

class DemoInvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $inventoryService = app(InventoryService::class);
        $invoiceService = app(InvoicePostingService::class);

        $product1 = Product::where('sku', 'FG-DEGREASE-1L')->firstOrFail();
        $product2 = Product::where('sku', 'FG-DET-5L')->first();
        $warehouse = Warehouse::where('code', 'WH-MAIN')->firstOrFail();
        $customer = Customer::walkInCustomer();
        $b2bCustomer = Customer::where('customer_type', 'b2b')->first() ?? $customer;

        // Ensure stock
        $inventoryService->addStock(
            productId: $product1->id,
            warehouseId: $warehouse->id,
            quantity: 100.0,
            unitCost: 65.0,
            movementType: 'opening_stock'
        );

        if ($product2) {
            $inventoryService->addStock(
                productId: $product2->id,
                warehouseId: $warehouse->id,
                quantity: 40.0,
                unitCost: 320.0,
                movementType: 'opening_stock'
            );
        }

        // 1. Walk-in Retail Invoice (Cash + UPI)
        $invoice1 = $invoiceService->postInvoice(
            rawCartItems: [
                [
                    'product_id' => $product1->id,
                    'quantity' => 4,
                    'discount_amount' => 0,
                ],
                [
                    'product_id' => $product2 ? $product2->id : $product1->id,
                    'quantity' => 1,
                    'discount_amount' => 0,
                ]
            ],
            customerId: $customer->id,
            warehouseId: $warehouse->id,
            priceTier: 'retail',
            payments: [
                [
                    'amount' => 500.00,
                    'payment_method' => 'cash',
                ],
                [
                    'amount' => 900.00, // Will be handled based on total
                    'payment_method' => 'upi',
                ]
            ],
            notes: 'Retail Counter Sale - Gudi Chemicals'
        );

        // 2. B2B Wholesale Invoice
        try {
            $invoiceService->postInvoice(
                rawCartItems: [
                    [
                        'product_id' => $product1->id,
                        'quantity' => 10,
                        'discount_amount' => 50.00,
                    ]
                ],
                customerId: $b2bCustomer->id,
                warehouseId: $warehouse->id,
                priceTier: 'wholesale',
                payments: [
                    [
                        'amount' => 1000.00,
                        'payment_method' => 'bank_transfer',
                    ]
                ],
                notes: 'Industrial Wholesale Dispatch - Net 30 Days'
            );
        } catch (\Throwable $e) {
            // ignore if exists
        }
    }
}
