<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['person', 'company']);
            $table->enum('document_type', ['DNI', 'RUC', 'PAS', 'CEX'])->nullable();
            $table->string('document_number')->unique()->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('business_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->text('address')->nullable();
            $table->string('ubigeo_code', 6)->nullable();
            $table->boolean('is_insurance_company')->default(false);
            $table->decimal('insurance_hourly_rate', 12, 2)->nullable();
            $table->decimal('insurance_panel_rate', 12, 2)->nullable();
            $table->boolean('receive_promotions')->default(true);
            $table->unsignedBigInteger('establishment_id');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('ubigeo_code')->references('code')->on('ubigeos')->nullOnDelete();
            $table->foreign('establishment_id')->references('id')->on('establishments');
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['type', 'document_type']);
            $table->index('document_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parties');
    }
};