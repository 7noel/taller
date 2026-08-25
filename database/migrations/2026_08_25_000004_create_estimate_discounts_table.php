<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimate_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimate_id');
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->enum('applied_to', ['subtotal', 'total'])->default('subtotal');
            $table->timestamps();

            $table->foreign('estimate_id')->references('id')->on('estimates')->cascadeOnDelete();

            $table->index('estimate_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimate_discounts');
    }
};