<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repair_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('service_category_id')->nullable();
            $table->enum('pricing_type', ['fixed', 'time_based'])->default('fixed');
            $table->string('uom', 5)->default('HUR'); // Código SUNAT Catálogo 03 (HUR = hora)
            $table->decimal('estimated_hours', 8, 2)->nullable();
            $table->decimal('min_hours', 8, 2)->nullable();
            $table->decimal('sell_price', 12, 2)->default(0);
            $table->string('currency', 3)->default('PEN');
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->string('cost_currency', 3)->default('PEN');
            $table->unsignedBigInteger('default_provider_id')->nullable();
            $table->boolean('is_outsourced')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('service_category_id')->references('id')->on('service_categories')->nullOnDelete();
            $table->foreign('default_provider_id')->references('id')->on('parties')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            $table->index('service_category_id');
            $table->index('pricing_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repair_services');
    }
};