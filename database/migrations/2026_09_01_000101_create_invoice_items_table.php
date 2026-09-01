<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('estimate_id')->nullable()->constrained('estimates')->nullOnDelete();
            $table->string('codigo_interno', 250)->nullable();
            $table->string('description', 250);
            $table->decimal('quantity', 12, 4);
            $table->decimal('unit_price', 12, 2); // sin IGV
            $table->decimal('price', 12, 2);      // con IGV
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2);   // cantidad * unit_price - discount
            $table->string('affectation_igv_type', 5)->default('10'); // 10 gravado, 20 exonerado, 30 inafecto, 40 gratuito
            $table->decimal('igv', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->string('uom', 5)->default('NIU'); // NIU producto, ZZ servicio
            $table->string('codigo_producto_sunat', 8)->nullable();
            $table->boolean('is_advance_line')->default(false); // línea de regularización de anticipo
            $table->foreignId('advance_invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
