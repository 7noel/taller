<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Catálogo global de subetapas del proceso de reparación.
        Schema::create('work_order_substages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Asignaciones de técnicos a subetapas dentro de una OT.
        Schema::create('work_order_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');
            $table->unsignedBigInteger('substage_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->decimal('hours', 8, 2)->default(0);
            $table->decimal('cost', 12, 2)->default(0);
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->foreign('substage_id')->references('id')->on('work_order_substages')->restrictOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            $table->index('work_order_id');
            $table->index(['substage_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_assignments');
        Schema::dropIfExists('work_order_substages');
    }
};
