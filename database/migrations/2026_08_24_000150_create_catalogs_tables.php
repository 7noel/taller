<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('part_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('part_brands', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Catálogo 03 SUNAT: unidades de medida (código ISO/UNE/ECE-20).
        // Se usa en repuestos (uom), servicios (uom) y como snapshot en estimate_items.
        Schema::create('unit_measures', function (Blueprint $table) {
            $table->string('code', 5)->primary();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Motivos de ingreso/salida de existencias (hardcodeado, igual que la
        // tabla inventory_transactions del facturador anterior). Solo se siembra.
        Schema::create('inventory_movement_reasons', function (Blueprint $table) {
            $table->string('code', 5)->primary();
            $table->string('name');
            $table->enum('type', ['input', 'output']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movement_reasons');
        Schema::dropIfExists('unit_measures');
        Schema::dropIfExists('part_brands');
        Schema::dropIfExists('part_categories');
        Schema::dropIfExists('service_categories');
    }
};