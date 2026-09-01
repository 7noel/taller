<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dispatch_id')->constrained('dispatches')->cascadeOnDelete();
            $table->string('codigo_interno', 250)->nullable();
            $table->string('description', 250);
            $table->decimal('quantity', 12, 4);
            $table->string('uom', 5)->default('NIU');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_items');
    }
};
