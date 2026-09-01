<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatches', function (Blueprint $table) {
            $table->id();
            // Identidad del documento (regla .clinerules/10)
            $table->foreignId('establishment_id')->nullable()->constrained('establishments')->nullOnDelete();
            $table->foreignId('document_series_id')->nullable()->constrained('document_series')->nullOnDelete();
            $table->string('document_type_code', 10)->default('09'); // 09 guía remisión remitente
            $table->string('document_serie', 10)->nullable();
            $table->unsignedInteger('document_number')->nullable();
            $table->string('document_sn', 30)->nullable()->index();

            // Clasificación
            $table->string('dispatch_type', 20)->default('remitente'); // remitente, transportista
            $table->foreignId('party_id')->nullable()->constrained('parties')->nullOnDelete(); // destinatario
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();

            // Traslado
            $table->string('motivo_traslado', 5); // 01 venta, 02 traslado entre establecimientos, etc.
            $table->string('descripcion_motivo_traslado', 250)->nullable();
            $table->string('modo_transporte', 5)->default('02'); // 01 público, 02 privado
            $table->date('fecha_de_traslado');
            $table->date('fecha_de_entrega')->nullable();
            $table->decimal('peso_total', 12, 3)->default(0);
            $table->string('unidad_peso', 5)->default('KGM');
            $table->unsignedInteger('numero_de_bultos')->nullable();

            // Puntos
            $table->string('punto_partida_ubigeo', 6)->nullable();
            $table->string('punto_partida_direccion', 250)->nullable();
            $table->string('punto_partida_codigo_establecimiento', 5)->default('0000');
            $table->string('punto_llegada_ubigeo', 6)->nullable();
            $table->string('punto_llegada_direccion', 250)->nullable();
            $table->string('punto_llegada_codigo_establecimiento', 5)->default('0000');

            // Transportista (solo transporte público)
            $table->string('transportista_documento_tipo', 5)->nullable();
            $table->string('transportista_documento_numero', 15)->nullable();
            $table->string('transportista_denominacion', 100)->nullable();

            // Conductor y vehículo
            $table->string('conductor_documento_tipo', 5)->nullable();
            $table->string('conductor_documento_numero', 15)->nullable();
            $table->string('conductor_nombre', 100)->nullable();
            $table->string('conductor_apellidos', 100)->nullable();
            $table->string('conductor_numero_licencia', 20)->nullable();
            $table->string('vehiculo_placa', 20)->nullable();
            $table->string('vehiculo_marca', 100)->nullable();
            $table->string('vehiculo_modelo', 100)->nullable();

            // Documento afectado (factura que ampara)
            $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();

            // Proveedor y estado
            $table->string('provider', 20)->default('nubefact'); // nubefact, propio
            $table->string('status', 20)->default('draft')->index(); // draft, emitted, accepted, rejected, voided
            $table->text('observations')->nullable();
            $table->dateTime('issued_at')->nullable();
            $table->string('external_id', 100)->nullable()->index();
            $table->boolean('accepted_by_sunat')->nullable();
            $table->text('sunat_description')->nullable();
            $table->text('sunat_note')->nullable();
            $table->string('sunat_responsecode', 10)->nullable();
            $table->text('enlace_pdf')->nullable();
            $table->text('enlace_xml')->nullable();
            $table->text('enlace_cdr')->nullable();
            $table->string('codigo_hash', 255)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatches');
    }
};
