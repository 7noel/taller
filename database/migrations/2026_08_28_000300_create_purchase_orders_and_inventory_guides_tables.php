<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Módulo 7 — Almacén/Compras/Kardex.
 *
 * Crea las tablas de órdenes de compra (OC01) y guías de inventario
 * (NIA1 / NSA1 / NTA1) con su identidad documental. Los vínculos a estos
 * documentos en stock_movements y part_orders viven en sus propias
 * migraciones create (consolidadas).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('establishment_id');
            $table->unsignedBigInteger('document_series_id')->nullable();
            $table->string('document_type_code', 10)->default('OC');
            $table->string('document_serie', 10)->nullable();
            $table->unsignedInteger('document_number')->nullable();
            $table->string('document_sn', 30)->nullable();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->date('order_date')->nullable();
            $table->date('expected_delivery')->nullable();
            $table->string('status', 20)->default('draft'); // draft | ordered | received | cancelled
            $table->string('currency', 3)->default('PEN');
            $table->decimal('exchange_rate', 12, 4)->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->string('provider_invoice', 30)->nullable(); // factura del proveedor (recepción)
            $table->string('provider_guide', 30)->nullable();   // guía del proveedor (recepción)
            $table->date('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('establishment_id')->references('id')->on('establishments');
            $table->foreign('document_series_id')->references('id')->on('document_series')->nullOnDelete();
            $table->foreign('provider_id')->references('id')->on('parties')->nullOnDelete();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            $table->index('document_sn');
            $table->index('status');
            $table->index('provider_id');
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('part_id')->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->string('uom', 5)->nullable(); // snapshot SUNAT
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->cascadeOnDelete();
            $table->foreign('part_id')->references('id')->on('parts')->nullOnDelete();

            $table->index('purchase_order_id');
        });

        // Guía de inventario: U2 (NIA1 ingreso), U3 (NSA1 salida), U4 (NTA1 transferencia).
        Schema::create('inventory_guides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('establishment_id');
            $table->unsignedBigInteger('document_series_id')->nullable();
            $table->string('document_type_code', 10); // U2 | U3 | U4
            $table->string('document_serie', 10)->nullable();
            $table->unsignedInteger('document_number')->nullable();
            $table->string('document_sn', 30)->nullable();
            $table->string('movement_reason_code', 5)->nullable(); // snapshot del motivo
            $table->unsignedBigInteger('origin_warehouse_id')->nullable();
            $table->unsignedBigInteger('destination_warehouse_id')->nullable();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->unsignedBigInteger('work_order_id')->nullable();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->string('provider_invoice', 30)->nullable();
            $table->string('provider_guide', 30)->nullable();
            $table->date('movement_date')->nullable();
            $table->string('status', 20)->default('posted'); // posted | cancelled
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('establishment_id')->references('id')->on('establishments');
            $table->foreign('document_series_id')->references('id')->on('document_series')->nullOnDelete();
            $table->foreign('movement_reason_code')->references('code')->on('inventory_movement_reasons')->nullOnDelete();
            $table->foreign('origin_warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
            $table->foreign('destination_warehouse_id')->references('id')->on('warehouses')->nullOnDelete();
            $table->foreign('provider_id')->references('id')->on('parties')->nullOnDelete();
            $table->foreign('work_order_id')->references('id')->on('work_orders')->nullOnDelete();
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            $table->index('document_sn');
            $table->index('document_type_code');
            $table->index('movement_date');
            $table->index('movement_reason_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_guides');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
