<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_settings', function (Blueprint $table) {
            $table->id();
            $table->string('ruc')->unique()->nullable();
            $table->string('razon_social')->nullable();
            $table->string('nombre_comercial')->nullable();
            $table->string('direccion')->nullable();
            $table->string('ubigeo_code', 6)->nullable();
            $table->string('telefono')->nullable();
            $table->string('celular')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->string('detraccion_account')->nullable();
            $table->decimal('igv_rate', 5, 4)->default(0.1800);
            $table->decimal('detraccion_rate', 5, 4)->default(0.1200);
            $table->enum('default_number_source', ['LOCAL', 'API'])->default('LOCAL');
            $table->enum('facturador_provider', ['local', 'nubefact', 'propio'])->default('local');
            $table->string('facturador_api_url')->nullable();
            $table->string('facturador_api_key')->nullable();
            $table->string('facturador_secret')->nullable();
            $table->string('whatsapp_api_url')->nullable();
            $table->string('whatsapp_api_token')->nullable();
            $table->string('whatsapp_instance_name')->nullable();
            $table->boolean('whatsapp_enabled')->default(false);
            $table->boolean('qc_require_assignments_completed')->default(true);
            // Configuración del mantenimiento preventivo (cálculo por kilometraje).
            $table->integer('maintenance_interval_km')->default(5000);
            $table->integer('maintenance_default_days')->default(120);
            $table->integer('maintenance_history_visits')->default(3);
            // Configuración de recordatorios automáticos por WhatsApp (switch maestro
            // + toggle por tipo + hora de envío). Ver ReminderService.
            $table->boolean('reminder_enabled')->default(true);
            $table->string('reminder_hour', 5)->default('09:00');
            $table->boolean('reminder_technical_review_enabled')->default(true);
            $table->integer('reminder_technical_review_days')->default(10);
            $table->boolean('reminder_maintenance_enabled')->default(true);
            $table->integer('reminder_maintenance_days')->default(7);
            $table->boolean('reminder_part_order_enabled')->default(true);
            $table->string('reminder_part_milestones', 100)->default('25,20,17,15,10,5');
            $table->boolean('reminder_estimate_enabled')->default(true);
            $table->integer('reminder_estimate_every_days')->default(3);
            $table->enum('camera_capture_mode', ['integrated', 'native'])->default('integrated');
            $table->timestamps();

            $table->foreign('ubigeo_code')->references('code')->on('ubigeos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};