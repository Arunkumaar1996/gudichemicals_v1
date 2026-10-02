<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->enum('customer_type', ['retail', 'wholesale'])->default('retail');
            $table->string('name', 150);
            $table->string('company_name', 150)->nullable();
            $table->string('phone', 25)->nullable()->index();
            $table->string('email', 100)->nullable();
            $table->string('gstin', 15)->nullable();
            $table->string('pan', 10)->nullable();
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state_code', 5)->default('27');
            $table->string('state_name', 100)->default('Maharashtra');
            $table->string('pincode', 10)->nullable();
            $table->decimal('credit_limit', 14, 2)->default(0.00);
            $table->decimal('opening_balance', 14, 2)->default(0.00);
            $table->decimal('current_balance', 14, 2)->default(0.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('sales_invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number', 50)->unique();
            $table->date('invoice_date');
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->enum('price_tier', ['retail', 'wholesale'])->default('retail');
            $table->boolean('is_interstate')->default(false);
            $table->string('place_of_supply', 100)->default('Maharashtra (27)');
            $table->decimal('subtotal', 14, 4);
            $table->decimal('discount_total', 14, 4)->default(0.0000);
            $table->decimal('taxable_amount', 14, 4);
            $table->decimal('cgst_amount', 14, 4)->default(0.0000);
            $table->decimal('sgst_amount', 14, 4)->default(0.0000);
            $table->decimal('igst_amount', 14, 4)->default(0.0000);
            $table->decimal('rounding_adjustment', 6, 2)->default(0.00);
            $table->decimal('grand_total', 14, 2);
            $table->decimal('paid_amount', 14, 2)->default(0.00);
            $table->decimal('due_amount', 14, 2)->default(0.00);
            $table->enum('payment_status', ['unpaid', 'partially_paid', 'paid'])->default('paid');
            $table->enum('status', ['draft', 'posted', 'cancelled'])->default('posted');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['invoice_date', 'status']);
            $table->index('customer_id');
        });

        Schema::create('sales_invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_invoice_id')->constrained('sales_invoices')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('inventory_batches')->nullOnDelete();
            $table->string('product_name', 255);
            $table->string('sku', 50);
            $table->string('hsn_code', 20);
            $table->decimal('quantity', 14, 4);
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->decimal('unit_price', 14, 4);
            $table->decimal('discount_amount', 14, 4)->default(0.0000);
            $table->decimal('taxable_amount', 14, 4);
            $table->decimal('gst_rate', 5, 2)->default(18.00);
            $table->decimal('cgst_rate', 5, 2)->default(9.00);
            $table->decimal('cgst_amount', 14, 4)->default(0.0000);
            $table->decimal('sgst_rate', 5, 2)->default(9.00);
            $table->decimal('sgst_amount', 14, 4)->default(0.0000);
            $table->decimal('igst_rate', 5, 2)->default(0.00);
            $table->decimal('igst_amount', 14, 4)->default(0.0000);
            $table->decimal('line_total', 14, 2);
            $table->boolean('is_free_item')->default(false);
            $table->decimal('cost_price', 14, 4)->default(0.0000); // for COGS margin analysis
            $table->timestamps();
        });

        Schema::create('sales_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_invoice_id')->constrained('sales_invoices')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->date('payment_date');
            $table->decimal('amount', 14, 2);
            $table->enum('payment_method', ['cash', 'upi', 'card', 'bank_transfer', 'credit']);
            $table->string('reference_no', 100)->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sales_returns', function (Blueprint $table) {
            $table->id();
            $table->string('return_number', 50)->unique();
            $table->foreignId('sales_invoice_id')->constrained('sales_invoices')->restrictOnDelete();
            $table->foreignId('customer_id')->constrained('customers')->restrictOnDelete();
            $table->date('return_date');
            $table->decimal('total_refund_amount', 14, 2);
            $table->enum('refund_status', ['refunded', 'credited_to_account'])->default('refunded');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('approved');
            $table->text('reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('sales_return_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sales_return_id')->constrained('sales_returns')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('inventory_batches')->nullOnDelete();
            $table->decimal('quantity', 14, 4);
            $table->enum('condition', ['saleable', 'damaged'])->default('saleable');
            $table->decimal('unit_price', 14, 4);
            $table->decimal('tax_rate', 5, 2)->default(18.00);
            $table->decimal('tax_amount', 14, 4)->default(0.0000);
            $table->decimal('line_total', 14, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_return_items');
        Schema::dropIfExists('sales_returns');
        Schema::dropIfExists('sales_payments');
        Schema::dropIfExists('sales_invoice_items');
        Schema::dropIfExists('sales_invoices');
        Schema::dropIfExists('customers');
    }
};
