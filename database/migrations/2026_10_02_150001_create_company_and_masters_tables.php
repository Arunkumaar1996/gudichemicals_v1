<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('company_name')->default('Gudi Chemicals');
            $table->string('trade_name')->nullable();
            $table->string('gstin', 15)->nullable();
            $table->string('pan', 10)->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 25)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state_code', 5)->default('27'); // Maharashtra default
            $table->string('state_name', 100)->default('Maharashtra');
            $table->string('pincode', 10)->nullable();
            $table->string('currency_symbol', 10)->default('₹');
            $table->string('logo_path')->nullable();
            $table->string('invoice_prefix', 20)->default('GC');
            $table->string('fy_code', 10)->default('2026-27');
            $table->string('bank_name', 100)->nullable();
            $table->string('bank_account_no', 50)->nullable();
            $table->string('bank_ifsc', 20)->nullable();
            $table->string('bank_branch', 100)->nullable();
            $table->string('upi_id', 100)->nullable();
            $table->text('terms_and_conditions')->nullable();
            $table->timestamps();
        });

        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 100);
            $table->string('location', 255)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('code', 20)->unique(); // LTR, KG, ML, GM, NOS, CAN, BTL, DRUM
            $table->boolean('is_fractional')->default(true); // e.g. LTR/KG allows 1.250, NOS does not
            $table->timestamps();
        });

        Schema::create('unit_conversions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_unit_id')->constrained('units')->cascadeOnDelete();
            $table->foreignId('to_unit_id')->constrained('units')->cascadeOnDelete();
            $table->decimal('conversion_factor', 14, 6); // 1 from_unit = conversion_factor * to_unit (e.g. 1 LTR = 1000 ML)
            $table->timestamps();
            $table->unique(['from_unit_id', 'to_unit_id']);
        });

        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 120)->unique();
            $table->foreignId('parent_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('hsn_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('description', 255)->nullable();
            $table->decimal('gst_rate', 5, 2)->default(18.00); // 0.00, 5.00, 12.00, 18.00, 28.00
            $table->timestamps();
        });

        Schema::create('document_sequences', function (Blueprint $table) {
            $table->id();
            $table->string('document_type', 50); // invoice, grn, po, batch, receipt, credit_note, debit_note
            $table->string('fy_code', 20); // 2026-27
            $table->string('prefix', 20); // GC, PO, GRN, BT, RCPT, CN
            $table->unsignedBigInteger('current_number')->default(0);
            $table->timestamps();
            $table->unique(['document_type', 'fy_code', 'prefix']);
        });

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('document_sequences');
        Schema::dropIfExists('hsn_codes');
        Schema::dropIfExists('product_categories');
        Schema::dropIfExists('unit_conversions');
        Schema::dropIfExists('units');
        Schema::dropIfExists('warehouses');
        Schema::dropIfExists('company_settings');
    }
};
