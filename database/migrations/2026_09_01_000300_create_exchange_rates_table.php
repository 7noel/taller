<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('currency', 3)->default('USD');
            $table->decimal('buy_rate', 12, 4)->default(0);
            $table->decimal('sell_rate', 12, 4)->default(0);
            $table->string('source')->nullable();
            $table->timestamps();

            $table->unique(['date', 'currency']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
