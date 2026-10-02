<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique();
            $table->string('barcode', 50)->nullable()->unique();
            $table->string('name', 255);
            $table->foreignId('category_id')->constrained('product_categories')->restrictOnDelete();
            $table->enum('item_type', [
                'raw_material',
                'semi_finished',
                'finished_goods',
                'packaging',
                'trading',
                'service'
            ]);
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->string('hsn_code', 20);
            $table->decimal('gst_rate', 5, 2)->default(18.00);
            $table->decimal('purchase_cost', 14, 4)->default(0.0000);
            $table->decimal('retail_price', 14, 4)->default(0.0000);
            $table->decimal('wholesale_price', 14, 4)->default(0.0000);
            $table->decimal('min_selling_price', 14, 4)->default(0.0000);
            $table->decimal('reorder_level', 14, 4)->default(0.0000);
            $table->boolean('has_batch_tracking')->default(true);
            $table->boolean('has_expiry_tracking')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('image_path')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['item_type', 'is_active']);
            $table->index('name');
        });

        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->string('batch_number', 50);
            $table->string('supplier_lot_number', 50)->nullable();
            $table->date('mfg_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('retest_date')->nullable();
            $table->decimal('cost_per_unit', 14, 4)->default(0.0000);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['product_id', 'batch_number']);
            $table->index(['expiry_date']);
        });

        Schema::create('stock_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('inventory_batches')->nullOnDelete();
            $table->decimal('quantity', 14, 4)->default(0.0000);
            $table->decimal('reserved_quantity', 14, 4)->default(0.0000);
            $table->timestamps();

            $table->unique(['product_id', 'warehouse_id', 'batch_id'], 'stock_balance_unique');
            $table->index(['product_id', 'warehouse_id']);
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->dateTime('movement_date');
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('inventory_batches')->nullOnDelete();
            $table->enum('movement_type', [
                'purchase_receipt',
                'purchase_return',
                'production_consumption',
                'production_output',
                'sales_issue',
                'sales_return',
                'transfer_in',
                'transfer_out',
                'adjustment_add',
                'adjustment_sub',
                'opening_stock'
            ]);
            $table->string('reference_type', 100)->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->decimal('quantity', 14, 4); // signed: positive for receipt/in, negative for consumption/issue
            $table->decimal('unit_cost', 14, 4)->default(0.0000);
            $table->decimal('balance_after', 14, 4)->default(0.0000);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['product_id', 'movement_date']);
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('adjustment_number', 50)->unique();
            $table->foreignId('warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->date('adjustment_date');
            $table->string('reason', 255);
            $table->enum('status', ['draft', 'approved', 'cancelled'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_adjustment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_adjustment_id')->constrained('stock_adjustments')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('inventory_batches')->nullOnDelete();
            $table->enum('type', ['add', 'subtract']);
            $table->decimal('quantity', 14, 4);
            $table->decimal('unit_cost', 14, 4)->default(0.0000);
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_adjustment_items');
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('stock_balances');
        Schema::dropIfExists('inventory_batches');
        Schema::dropIfExists('products');
    }
};
