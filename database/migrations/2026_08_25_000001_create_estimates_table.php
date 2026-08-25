<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('check_in_id')->nullable();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('insurance_company_id')->nullable();
            $table->string('claim_number')->nullable();
            $table->string('service_type')->nullable();
            $table->unsignedBigInteger('advisor_id')->nullable();
            $table->unsignedBigInteger('establishment_id');
            $table->unsignedBigInteger('document_series_id')->nullable();
            $table->string('document_type_code')->nullable();
            $table->string('document_serie')->nullable();
            $table->unsignedInteger('document_number')->nullable();
            $table->string('document_sn')->nullable();
            $table->integer('work_days')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('comments')->nullable();
            $table->decimal('hourly_rate', 12, 2)->default(0);
            $table->decimal('panel_rate', 12, 2)->default(0);
            $table->string('currency', 3)->default('PEN');
            $table->decimal('exchange_rate', 12, 4)->default(1);
            $table->enum('global_discount_type', ['percentage', 'fixed'])->nullable();
            $table->decimal('global_discount_value', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('taxable_base', 12, 2)->default(0);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('status')->default('draft');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('check_in_id')->references('id')->on('check_ins')->nullOnDelete();
            $table->foreign('vehicle_id')->references('id')->on('vehicles')->restrictOnDelete();
            $table->foreign('client_id')->references('id')->on('parties')->restrictOnDelete();
            $table->foreign('insurance_company_id')->references('id')->on('parties')->nullOnDelete();
            $table->foreign('advisor_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('establishment_id')->references('id')->on('establishments')->restrictOnDelete();
            $table->foreign('document_series_id')->references('id')->on('document_series')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            $table->unique('document_sn');
            $table->index('check_in_id');
            $table->index('vehicle_id');
            $table->index('client_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimates');
    }
};