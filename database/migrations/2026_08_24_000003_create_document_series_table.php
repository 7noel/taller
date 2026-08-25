<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_series', function (Blueprint $table) {
            $table->id();
            $table->foreignId('establishment_id')->constrained('establishments');
            $table->foreignId('document_type_id')->constrained('document_types');
            $table->string('prefix_serie');
            $table->integer('current_number')->default(0);
            $table->enum('number_source', ['LOCAL', 'API'])->default('LOCAL');
            $table->boolean('status')->default(true);
            $table->timestamps();

            $table->unique(['establishment_id', 'document_type_id', 'prefix_serie'], 'doc_series_est_doc_prefix_unique');
            $table->index('document_type_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_series');
    }
};