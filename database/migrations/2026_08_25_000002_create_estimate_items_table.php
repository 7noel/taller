<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimate_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimate_id');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('part_id')->nullable();
            // Snapshot SUNAT Catálogo 03: unidad de medida de la línea al momento de cotizar.
            $table->string('item_type', 20)->default('service'); // service | part | third_party
            $table->unsignedBigInteger('service_category_id')->nullable();
            $table->unsignedBigInteger('part_category_id')->nullable();
            $table->string('uom', 5)->nullable();
            $table->string('description')->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_price', 12, 4)->default(0);
            $table->decimal('discount_pct', 5, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('net_line', 12, 2)->default(0);
            $table->decimal('iva_line', 12, 2)->default(0);
            $table->decimal('total_line', 12, 2)->default(0);
            $table->enum('supply_source', ['internal', 'external', 'insurance'])->default('internal');
            $table->decimal('cost_price', 12, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('estimate_id')->references('id')->on('estimates')->cascadeOnDelete();
            $table->foreign('service_id')->references('id')->on('repair_services')->nullOnDelete();
            $table->foreign('part_id')->references('id')->on('parts')->nullOnDelete();
            $table->foreign('service_category_id')->references('id')->on('service_categories')->nullOnDelete();
            $table->foreign('part_category_id')->references('id')->on('part_categories')->nullOnDelete();

            $table->index('estimate_id');
            $table->index('service_id');
            $table->index('part_id');
            $table->index('item_type');
            $table->index('service_category_id');
            $table->index('part_category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimate_items');
    }
};