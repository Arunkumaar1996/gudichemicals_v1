<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('formulas', function (Blueprint $table) {
            $table->id();
            $table->string('formula_code', 50)->unique();
            $table->string('name', 200);
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete(); // output product
            $table->unsignedInteger('version')->default(1);
            $table->decimal('standard_batch_qty', 14, 4); // e.g. 1000 Litres
            $table->foreignId('output_unit_id')->constrained('units')->restrictOnDelete();
            $table->decimal('expected_yield_pct', 5, 2)->default(100.00);
            $table->decimal('process_loss_pct', 5, 2)->default(0.00);
            $table->boolean('is_approved')->default(true);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['product_id', 'version']);
        });

        Schema::create('formula_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('formula_id')->constrained('formulas')->cascadeOnDelete();
            $table->foreignId('ingredient_product_id')->constrained('products')->restrictOnDelete();
            $table->decimal('quantity', 14, 4); // quantity required for standard batch
            $table->foreignId('unit_id')->constrained('units')->restrictOnDelete();
            $table->decimal('wastage_pct', 5, 2)->default(0.00);
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number', 50)->unique();
            $table->foreignId('formula_id')->constrained('formulas')->restrictOnDelete();
            $table->foreignId('output_product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('target_warehouse_id')->constrained('warehouses')->restrictOnDelete();
            $table->decimal('planned_qty', 14, 4);
            $table->decimal('actual_qty', 14, 4)->default(0.0000);
            $table->date('order_date');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('completion_date')->nullable();
            $table->enum('status', ['draft', 'in_progress', 'qc_pending', 'completed', 'cancelled'])->default('draft');
            $table->json('formula_snapshot'); // Complete freeze of recipe at batch initiation
            $table->decimal('total_material_cost', 14, 4)->default(0.0000);
            $table->decimal('packaging_cost', 14, 4)->default(0.0000);
            $table->decimal('overhead_cost', 14, 4)->default(0.0000);
            $table->decimal('unit_production_cost', 14, 4)->default(0.0000);
            $table->foreignId('batch_id')->nullable()->constrained('inventory_batches')->nullOnDelete(); // created lot
            $table->foreignId('operator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('production_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained('production_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained('inventory_batches')->nullOnDelete();
            $table->decimal('planned_qty', 14, 4);
            $table->decimal('actual_consumed_qty', 14, 4);
            $table->decimal('unit_cost', 14, 4)->default(0.0000);
            $table->decimal('total_cost', 14, 4)->default(0.0000);
            $table->timestamps();
        });

        Schema::create('production_quality_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('production_order_id')->constrained('production_orders')->cascadeOnDelete();
            $table->string('parameter_name', 100); // pH, Viscosity, Specific Gravity, Appearance, Odor, Active Matter %
            $table->string('standard_specification', 150);
            $table->string('observed_value', 150);
            $table->boolean('is_passed')->default(true);
            $table->foreignId('tested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_quality_checks');
        Schema::dropIfExists('production_consumptions');
        Schema::dropIfExists('production_orders');
        Schema::dropIfExists('formula_items');
        Schema::dropIfExists('formulas');
    }
};
