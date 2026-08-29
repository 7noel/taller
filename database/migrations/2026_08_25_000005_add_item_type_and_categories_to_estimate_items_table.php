<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estimate_items', function (Blueprint $table) {
            $table->string('item_type', 20)->default('service')->after('part_id');
            $table->unsignedBigInteger('service_category_id')->nullable()->after('item_type');
            $table->unsignedBigInteger('part_category_id')->nullable()->after('service_category_id');
            // Snapshot SUNAT Catálogo 03: unidad de medida de la línea al momento de cotizar.
            $table->string('uom', 5)->nullable()->after('part_category_id');

            $table->foreign('service_category_id')->references('id')->on('service_categories')->nullOnDelete();
            $table->foreign('part_category_id')->references('id')->on('part_categories')->nullOnDelete();

            $table->index('item_type');
            $table->index('service_category_id');
            $table->index('part_category_id');
        });
    }

    public function down(): void
    {
        Schema::table('estimate_items', function (Blueprint $table) {
            $table->dropForeign(['service_category_id']);
            $table->dropForeign(['part_category_id']);
            $table->dropIndex(['item_type']);
            $table->dropIndex(['service_category_id']);
            $table->dropIndex(['part_category_id']);
            $table->dropColumn(['item_type', 'service_category_id', 'part_category_id']);
        });
    }
};