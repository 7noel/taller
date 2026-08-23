<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('check_in_checklist_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('check_in_id')->constrained('check_ins')->cascadeOnDelete();
            $table->foreignId('checklist_item_id')->constrained('check_in_checklist_items');
            $table->enum('status', ['good', 'regular', 'bad', 'not_applicable'])->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();

            $table->unique(['check_in_id', 'checklist_item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('check_in_checklist_results');
    }
};