<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 50)->unique();
            $table->enum('type', [
                'bogo',             // Buy 1 Get 1 Free
                'b2g1',             // Buy 2 Get 1 Free
                'bxgy',             // Buy X Get Y
                'percent_discount', // Percentage off
                'flat_discount',    // Flat off order/item
                'combo',            // Special Combo bundle
                'slab_discount'     // Volume tier discount
            ]);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('min_order_value', 14, 2)->default(0.00);
            $table->integer('priority')->default(1);
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('promotion_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promotion_id')->constrained('promotions')->cascadeOnDelete();
            $table->foreignId('buy_product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->decimal('buy_quantity', 14, 4)->default(1.0000);
            $table->foreignId('get_product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->decimal('get_quantity', 14, 4)->default(1.0000);
            $table->decimal('discount_pct', 5, 2)->default(0.00);
            $table->decimal('discount_flat', 14, 2)->default(0.00);
            $table->enum('customer_type', ['all', 'retail', 'wholesale'])->default('all');
            $table->json('conditions_json')->nullable();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('expense_number', 50)->unique();
            $table->foreignId('expense_category_id')->constrained('expense_categories')->restrictOnDelete();
            $table->date('expense_date');
            $table->decimal('amount', 14, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'upi', 'card'])->default('cash');
            $table->string('paid_to', 150);
            $table->string('reference_no', 100)->nullable();
            $table->string('receipt_voucher_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100);
            $table->string('subject_type', 100)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->text('description')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->json('properties')->nullable();
            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
            $table->index('user_id');
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('channel', ['whatsapp', 'email']);
            $table->string('recipient', 100);
            $table->string('reference_type', 100)->nullable(); // e.g. App\Models\SalesInvoice
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->enum('status', ['queued', 'sent', 'failed']);
            $table->string('provider_message_id', 150)->nullable();
            $table->text('error_message')->nullable();
            $table->json('payload_snapshot')->nullable();
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('promotion_rules');
        Schema::dropIfExists('promotions');
    }
};
