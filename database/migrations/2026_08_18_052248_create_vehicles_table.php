<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('plate')->unique();
            $table->unsignedBigInteger('client_id');
            $table->string('brand');
            $table->string('model');
            $table->enum('body_type', ['sedan', 'suv', 'pickup', 'camioneta', 'camion', 'moto']);
            $table->string('color')->nullable();
            $table->string('vin')->nullable();
            $table->string('engine_number')->nullable();
            $table->integer('year')->nullable();
            $table->unsignedBigInteger('establishment_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('client_id')->references('id')->on('clients')->cascadeOnDelete();
            $table->foreign('establishment_id')->references('id')->on('establishments');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            $table->index('client_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};