<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('establishment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_series_id')->nullable()->constrained('document_series')->nullOnDelete();
            $table->string('document_type_code', 10)->default('CST');
            $table->string('document_serie', 20)->nullable();
            $table->unsignedInteger('document_number')->nullable();
            $table->string('document_sn', 30)->nullable()->index();
            $table->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('provider_id')->constrained('parties')->cascadeOnDelete();
            // Soporte de moneda (PEN/USD) + snapshot del tipo de cambio (soles por 1 USD).
            $table->string('currency', 3)->default('PEN');
            $table->decimal('exchange_rate', 12, 4)->default(1);
            $table->date('execution_date');
            $table->text('description');
            $table->decimal('agreed_amount', 12, 2)->default(0);
            $table->decimal('discount_applied', 12, 2)->default(0);
            $table->decimal('base_amount', 12, 2)->default(0);
            $table->decimal('igv_rate', 5, 4)->default(0.1800);
            $table->decimal('igv_amount', 12, 2)->default(0);
            $table->decimal('total_with_igv', 12, 2)->default(0);
            $table->decimal('detraction_rate', 5, 4)->default(0.1200);
            $table->decimal('detraction_amount', 12, 2)->default(0);
            $table->decimal('total_payable', 12, 2)->default(0);
            $table->enum('status', ['pending', 'completed', 'liquidated'])->default('pending')->index();
            $table->foreignId('provider_settlement_id')->nullable()->constrained('provider_settlements')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_vouchers');
    }
};
