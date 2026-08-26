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
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('code')->unique();

            // Credenciales Evolution API (WhatsApp) por establecimiento.
            // Se copian desde company_settings al crear (ver EstablishmentController).
            $table->string('whatsapp_api_url')->nullable();
            $table->string('whatsapp_api_token')->nullable();
            $table->string('whatsapp_instance_name')->nullable();
            $table->boolean('whatsapp_enabled')->default(false);

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
