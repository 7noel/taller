<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->unsignedBigInteger('client_id');
            $table->unsignedBigInteger('establishment_id');
            $table->unsignedBigInteger('document_series_id')->nullable();
            $table->string('document_type_code')->nullable();
            $table->string('document_serie')->nullable();
            $table->unsignedInteger('document_number')->nullable();
            $table->string('document_sn')->nullable();
            $table->string('status')->default('open');
            $table->date('start_date')->nullable();
            $table->date('estimated_end_date')->nullable();
            // ===== Entrega y encuesta de satisfacción =====
            $table->timestamp('delivered_at')->nullable();
            $table->unsignedBigInteger('delivered_by')->nullable();
            $table->timestamp('survey_sent_at')->nullable();
            $table->string('survey_sent_to')->nullable();
            $table->string('survey_sent_to_phone')->nullable();
            $table->string('last_sent_to')->nullable();
            $table->string('last_sent_to_phone')->nullable();
            $table->timestamp('last_sent_at')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->restrictOnDelete();
            $table->foreign('client_id')->references('id')->on('parties')->restrictOnDelete();
            $table->foreign('establishment_id')->references('id')->on('establishments')->restrictOnDelete();
            $table->foreign('document_series_id')->references('id')->on('document_series')->nullOnDelete();
            $table->foreign('delivered_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            $table->unique('document_sn');
            $table->index(['vehicle_id', 'status']);
            $table->index('client_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_orders');
    }
};
