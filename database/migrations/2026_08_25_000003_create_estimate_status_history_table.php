<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimate_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimate_id');
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('comments')->nullable();
            $table->timestamps();

            $table->foreign('estimate_id')->references('id')->on('estimates')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            $table->index('estimate_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimate_status_history');
    }
};