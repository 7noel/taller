<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('check_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->foreignId('client_id')->nullable()->constrained('parties');
            $table->foreignId('insurance_company_id')->nullable()->constrained('parties');
            $table->foreignId('establishment_id')->constrained('establishments');
            // Identidad del documento (regla .clinerules/10)
            $table->foreignId('document_series_id')->nullable()->constrained('document_series');
            $table->string('document_type_code')->nullable();
            $table->string('document_serie')->nullable();
            $table->unsignedInteger('document_number')->nullable();
            $table->string('document_sn')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');

            $table->string('service_type'); // siniestro, preventivo, correctivo, otro
            $table->string('claim_number')->nullable();
            $table->integer('mileage')->nullable();
            $table->string('fuel_level')->nullable(); // reserva, cuarto, medio, tres_cuartos, full
            $table->string('property_card')->nullable(); // fisica, virtual, no_tiene
            $table->date('soat_expiration')->nullable();
            $table->date('technical_review_expiration')->nullable();
            $table->integer('keys_count')->default(0);
            $table->boolean('has_remote_control')->default(false);
            $table->text('client_request')->nullable();
            $table->text('observations')->nullable();
            $table->enum('status', ['draft', 'pending_approval', 'approved', 'rejected', 'closed'])->default('draft');
            // Vincula cada visita física (check-in original y reingresos) a la OT.
            $table->unsignedBigInteger('work_order_id')->nullable();

            // ===== Quién aprobó/rechazó (usuario interno o cliente vía portal) =====
            $table->unsignedBigInteger('approved_by_user_id')->nullable();
            $table->string('approved_by_recipient')->nullable(); // snapshot: nombre del cliente al que se envió el enlace
            $table->string('approved_by_phone')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('rejected_by_user_id')->nullable();
            $table->string('rejected_by_recipient')->nullable();
            $table->string('rejected_by_phone')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('rejected_at')->nullable();

            // ===== Último envío del enlace por WhatsApp (se graba al enviar) =====
            $table->string('last_sent_to')->nullable();
            $table->string('last_sent_to_phone')->nullable();
            $table->timestamp('last_sent_at')->nullable();

            // ===== Cierre (el vehículo salió del taller) =====
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->boolean('appointment_associated')->default(false);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('work_order_id')->references('id')->on('work_orders')->nullOnDelete();
            $table->foreign('approved_by_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('rejected_by_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('closed_by')->references('id')->on('users')->nullOnDelete();

            $table->index('document_sn');
            $table->index('document_serie');
            $table->index('document_number');
            $table->index('work_order_id');
            $table->index(['vehicle_id', 'status']);
            $table->index('establishment_id');
            $table->index('service_type');
            $table->index('approved_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_ins');
    }
};