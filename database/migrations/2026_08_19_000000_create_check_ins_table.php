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

            $table->timestamps();
            $table->softDeletes();

            $table->index(['vehicle_id', 'status']);
            $table->index('establishment_id');
            $table->index('service_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_ins');
    }
};