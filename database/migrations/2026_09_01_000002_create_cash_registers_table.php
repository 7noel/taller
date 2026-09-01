<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_registers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('establishment_id')->nullable()->constrained('establishments')->nullOnDelete();
            $table->string('name', 100);
            $table->dateTime('opening_date');
            $table->decimal('opening_amount', 12, 2)->default(0);
            $table->dateTime('closing_date')->nullable();
            $table->decimal('closing_amount', 12, 2)->nullable();
            $table->decimal('expected_amount', 12, 2)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('open')->index(); // open, closed
            $table->foreignId('opened_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_registers');
    }
};
