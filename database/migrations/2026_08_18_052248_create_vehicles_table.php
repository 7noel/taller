<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate')->unique();
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('model_id');
            $table->string('color')->nullable();
            $table->string('vin')->nullable();
            $table->string('engine_number')->nullable();
            $table->integer('year')->nullable();
            $table->string('body_type')->nullable();
            // Vencimiento del SOAT: se carga y actualiza desde los check-ins (ingresos) y vehículos.
            $table->date('soat_expiration')->nullable();
            $table->date('technical_review_date')->nullable();
            $table->integer('review_reminder_days')->default(15);
            // Mantenimiento preventivo: última visita, próxima visita (calculada o manual)
            // y configuración del cálculo por kilometraje (ver MaintenanceService).
            $table->date('last_maintenance_date')->nullable();
            $table->integer('last_maintenance_mileage')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->integer('maintenance_reminder_days')->default(15);
            $table->string('maintenance_source', 20)->default('calculated'); // calculated | manual

            // Token de acceso público del vehículo (portal del cliente).
            // Se genera al crear el vehículo (ver VehicleService::create).
            $table->string('access_token', 64)->unique()->nullable();
            $table->timestamp('access_token_created_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('brand_id')->references('id')->on('brands');
            $table->foreign('model_id')->references('id')->on('models');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            $table->index('plate');
            $table->index('model_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};