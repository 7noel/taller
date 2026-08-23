<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('check_in_checklist_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category')->nullable(); // EXTERIOR, MOTOR, INTERIOR, HERRAMIENTAS/EMERGENCIA
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_in_checklist_items');
    }
};