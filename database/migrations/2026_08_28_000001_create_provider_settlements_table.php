<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider_settlements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('establishment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('document_series_id')->nullable()->constrained('document_series')->nullOnDelete();
            $table->string('document_type_code', 10)->default('LST');
            $table->string('document_serie', 20)->nullable();
            $table->unsignedInteger('document_number')->nullable();
            $table->string('document_sn', 30)->nullable()->index();
            $table->foreignId('provider_id')->constrained('parties')->cascadeOnDelete();
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('global_discount', 12, 2)->default(0);
            $table->string('discount_reason')->nullable();
            $table->decimal('base_amount', 12, 2)->default(0);
            $table->decimal('igv_rate', 5, 4)->default(0.1800);
            $table->decimal('igv_amount', 12, 2)->default(0);
            $table->decimal('total_with_igv', 12, 2)->default(0);
            $table->decimal('detraction_rate', 5, 4)->default(0.1200);
            $table->decimal('detraction_amount', 12, 2)->default(0);
            $table->decimal('total_payable', 12, 2)->default(0);
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft')->index();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('paid_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider_settlements');
    }
};
