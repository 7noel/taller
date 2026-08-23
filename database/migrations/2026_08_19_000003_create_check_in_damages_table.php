<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('check_in_damages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_in_id')->constrained('check_ins')->cascadeOnDelete();
            $table->enum('damage_type', ['scratch', 'dent', 'crack']);
            $table->enum('side', ['front', 'rear', 'left', 'right', 'top']);
            $table->integer('pos_x')->nullable(); // coordenada relativa % (0-100)
            $table->integer('pos_y')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_in_damages');
    }
};