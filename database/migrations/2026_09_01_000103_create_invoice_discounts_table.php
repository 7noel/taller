<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_discounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->string('code', 5);   // 02 global, 04 por anticipos
            $table->string('description', 250)->nullable();
            $table->decimal('factor', 8, 5)->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('base', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_discounts');
    }
};
