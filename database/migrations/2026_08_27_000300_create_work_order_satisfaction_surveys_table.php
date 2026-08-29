<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Encuesta de satisfacción respondida por el cliente vía portal público.
        Schema::create('work_order_satisfaction_surveys', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');
            $table->unsignedBigInteger('form_template_id')->nullable();
            $table->json('answers')->nullable();
            $table->string('respondent_name')->nullable();
            $table->string('respondent_phone')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->foreign('form_template_id')->references('id')->on('form_templates')->nullOnDelete();

            $table->index('work_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_satisfaction_surveys');
    }
};
