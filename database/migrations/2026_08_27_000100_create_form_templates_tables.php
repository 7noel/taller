<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Plantilla de formulario (control de calidad / encuesta de satisfacción)
        // configurable por establecimiento. establishment_id = null => plantilla global.
        Schema::create('form_templates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('establishment_id')->nullable();
            $table->string('type'); // quality_control | satisfaction_survey
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('establishment_id')->references('id')->on('establishments')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            $table->unique(['establishment_id', 'type']);
            $table->index(['type', 'is_active']);
        });

        Schema::create('form_template_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_template_id');
            $table->string('name');
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('form_template_id')->references('id')->on('form_templates')->cascadeOnDelete();
            $table->index(['form_template_id', 'order']);
        });

        Schema::create('form_template_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_template_section_id');
            $table->string('type'); // select | number | checkbox | radio | textarea | text
            $table->string('key'); // identificador estable para guardar respuestas (answers JSON)
            $table->string('label');
            $table->json('options')->nullable(); // [{value,label}] para select/radio
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('form_template_section_id')->references('id')->on('form_template_sections')->cascadeOnDelete();
            $table->unique(['form_template_section_id', 'key']);
            $table->index(['form_template_section_id', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('form_template_items');
        Schema::dropIfExists('form_template_sections');
        Schema::dropIfExists('form_templates');
    }
};
