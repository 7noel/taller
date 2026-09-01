<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('establishments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('ubigeo_code', 6)->nullable();
            $table->string('phone')->nullable();
            $table->string('celular')->nullable();
            $table->string('email')->nullable();
            $table->decimal('igv_rate', 5, 4)->default(0.1800);
            $table->string('base_currency', 3)->default('PEN');
            $table->boolean('prices_include_tax')->default(true);
            $table->decimal('default_hourly_rate', 12, 2)->default(0);
            $table->decimal('default_panel_rate', 12, 2)->default(0);
            $table->string('code')->unique();

            // Credenciales Evolution API (WhatsApp) por establecimiento.
            // Se copian desde company_settings al crear (ver EstablishmentController).
            $table->string('whatsapp_api_url')->nullable();
            $table->string('whatsapp_api_token')->nullable();
            $table->string('whatsapp_instance_name')->nullable();
            $table->boolean('whatsapp_enabled')->default(false);

            $table->foreign('ubigeo_code')->references('code')->on('ubigeos')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('establishments');
    }
};
