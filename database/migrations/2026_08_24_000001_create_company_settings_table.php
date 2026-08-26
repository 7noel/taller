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
            $table->enum('default_number_source', ['LOCAL', 'API'])->default('LOCAL');
            $table->enum('facturador_provider', ['local', 'nubefact', 'propio'])->default('local');
            $table->string('facturador_api_url')->nullable();
            $table->string('facturador_api_key')->nullable();
            $table->string('facturador_secret')->nullable();
            $table->string('whatsapp_api_url')->nullable();
            $table->string('whatsapp_api_token')->nullable();
            $table->string('whatsapp_instance_name')->nullable();
            $table->boolean('whatsapp_enabled')->default(false);
            $table->timestamps();

            $table->foreign('ubigeo_code')->references('code')->on('ubigeos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_settings');
    }
};