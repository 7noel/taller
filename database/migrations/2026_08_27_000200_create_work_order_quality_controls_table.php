<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Una fila por revisión de control de calidad (historial). Las respuestas del
        // formulario se guardan en `answers` (JSON keyed por form_template_items.key).
        Schema::create('work_order_quality_controls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');
            $table->unsignedBigInteger('form_template_id')->nullable();
            $table->string('result'); // approved | rejected
            $table->string('rejection_reason')->nullable();
            $table->string('rejection_details')->nullable();
            $table->json('answers')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->foreign('form_template_id')->references('id')->on('form_templates')->nullOnDelete();
            $table->foreign('reviewed_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['work_order_id', 'result']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_quality_controls');
    }
};
