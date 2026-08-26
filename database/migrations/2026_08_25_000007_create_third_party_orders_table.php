<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('third_party_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimate_id');
            $table->string('description');
            $table->decimal('amount_without_iva', 12, 2)->default(0);
            $table->string('provider_name')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('estimate_id')->references('id')->on('estimates')->cascadeOnDelete();
            $table->index('estimate_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('third_party_orders');
    }
};