<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('part_id');
            $table->unsignedBigInteger('warehouse_id');
            $table->enum('type', ['entry', 'exit', 'adjustment']);
            $table->decimal('quantity', 12, 2);
            $table->string('currency', 3)->default('PEN');
            $table->decimal('exchange_rate', 12, 4)->nullable();
            $table->decimal('unit_cost', 12, 2)->default(0);
            $table->decimal('total_cost', 12, 2)->default(0);
            $table->decimal('unit_cost_pen', 12, 2)->default(0);
            $table->decimal('total_cost_pen', 12, 2)->default(0);
            $table->string('document_type')->nullable();
            $table->unsignedBigInteger('document_id')->nullable();
            $table->string('reference')->nullable();
            // Vínculos a documentos (guía de inventario, OC, OT) y motivo de movimiento.
            $table->string('movement_reason_code', 5)->nullable();
            $table->unsignedBigInteger('inventory_guide_id')->nullable();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('work_order_id')->nullable();
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('part_id')->references('id')->on('parts')->cascadeOnDelete();
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('movement_reason_code')->references('code')->on('inventory_movement_reasons')->nullOnDelete();
            $table->foreign('inventory_guide_id')->references('id')->on('inventory_guides')->nullOnDelete();
            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->nullOnDelete();
            $table->foreign('work_order_id')->references('id')->on('work_orders')->nullOnDelete();

            $table->index(['part_id', 'warehouse_id']);
            $table->index(['document_type', 'document_id']);
            $table->index('created_at');
            $table->index('movement_reason_code');
            $table->index('inventory_guide_id');
            $table->index('purchase_order_id');
            $table->index('work_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};