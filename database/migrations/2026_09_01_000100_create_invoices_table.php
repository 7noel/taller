<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            // Identidad del documento (regla .clinerules/10)
            $table->foreignId('establishment_id')->nullable()->constrained('establishments')->nullOnDelete();
            $table->foreignId('document_series_id')->nullable()->constrained('document_series')->nullOnDelete();
            $table->string('document_type_code', 10); // 01 factura, 03 boleta, 07 NC, 08 ND
            $table->string('document_serie', 10)->nullable();
            $table->unsignedInteger('document_number')->nullable();
            $table->string('document_sn', 30)->nullable()->index();

            // Origen y clasificación
            $table->string('invoice_type', 20)->default('free')->index(); // advance, franchise, insurance, regular, free
            $table->string('origin', 20)->default('free')->index();       // estimate, ot, free
            $table->foreignId('work_order_id')->nullable()->constrained('work_orders')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();

            // Receptor (cliente o aseguradora)
            $table->foreignId('party_id')->nullable()->constrained('parties')->nullOnDelete();

            // Notas que modifican (NC/ND)
            $table->foreignId('related_invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->string('documento_que_se_modifica_tipo', 10)->nullable();
            $table->string('documento_que_se_modifica_serie', 10)->nullable();
            $table->unsignedInteger('documento_que_se_modifica_numero')->nullable();
            $table->string('tipo_de_nota', 5)->nullable();
            $table->string('motivo_nota', 255)->nullable();

            // Proveedor y operación SUNAT
            $table->string('provider', 20)->default('nubefact'); // nubefact, propio
            $table->unsignedInteger('sunat_transaction')->default(1); // 1 venta interna, 4 anticipos

            // Moneda y montos
            $table->string('currency', 3)->default('PEN');
            $table->decimal('exchange_rate', 12, 3)->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('taxable_base', 12, 2)->default(0);
            $table->decimal('iva', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('total_advances', 12, 2)->nullable();
            $table->text('observations')->nullable();

            // Estado y emisión
            $table->string('status', 20)->default('draft')->index(); // draft, pending, emitted, accepted, rejected, voided
            $table->date('invoice_date')->nullable();
            $table->dateTime('issued_at')->nullable();
            $table->boolean('enviar_automaticamente_a_la_sunat')->default(true);
            $table->boolean('enviar_automaticamente_al_cliente')->default(false);

            // Respuesta del proveedor
            $table->string('external_id', 100)->nullable()->index();
            $table->boolean('accepted_by_sunat')->nullable();
            $table->text('sunat_description')->nullable();
            $table->text('sunat_note')->nullable();
            $table->string('sunat_responsecode', 10)->nullable();
            $table->text('enlace_pdf')->nullable();
            $table->text('enlace_xml')->nullable();
            $table->text('enlace_cdr')->nullable();
            $table->text('cadena_qr')->nullable();
            $table->string('codigo_hash', 255)->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
