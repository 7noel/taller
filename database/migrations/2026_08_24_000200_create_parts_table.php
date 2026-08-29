<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('sku')->unique();
            $table->string('manufacturer_code')->nullable();
            $table->string('barcode')->nullable()->unique();
            $table->unsignedBigInteger('part_brand_id')->nullable();
            $table->unsignedBigInteger('part_category_id')->nullable();
            $table->string('uom', 5)->default('NIU'); // Código SUNAT Catálogo 03 (NIU = unidad)
            $table->integer('min_stock')->default(0);
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->string('cost_currency', 3)->default('PEN');
            $table->decimal('sell_price', 12, 2)->default(0);
            $table->string('currency', 3)->default('PEN');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('part_brand_id')->references('id')->on('part_brands')->nullOnDelete();
            $table->foreign('part_category_id')->references('id')->on('part_categories')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            $table->index('part_category_id');
            $table->index('part_brand_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parts');
    }
};