<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reminder_logs', function (Blueprint $table) {
            $table->id();
            // Tipo de recordatorio (technical_review|maintenance|part_order|estimate|...)
            // y entidad a la que apunta (vehicle|part_order|estimate).
            $table->string('type', 40);
            $table->string('target_type', 40);
            $table->unsignedBigInteger('target_id');
            // Fecha en la que el recordatorio estaba programado (día exacto del envío).
            // La unique (type, target_type, target_id, trigger_date) garantiza
            // idempotencia: un recordatorio por entidad y fecha de disparo.
            $table->date('trigger_date');
            $table->string('recipient_type', 20)->default('client'); // client|advisor
            $table->string('phone', 30)->nullable();
            $table->string('recipient_name')->nullable();
            $table->text('message')->nullable();
            $table->string('status', 20)->default('pending'); // pending|sent|failed
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['type', 'target_type', 'target_id', 'trigger_date'], 'reminder_logs_unique');
            $table->index(['type', 'status']);
            $table->index(['trigger_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reminder_logs');
    }
};
