<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Campos especiales para cubrir los casos de responsabilidad del taller:
     *  - Garantía: presupuesto NO facturable (is_chargeable=false) vinculado al
     *    presupuesto original (warranty_of_estimate_id) con liability='workshop'.
     *  - Daño interno / siniestro por mala maniobra: liability + responsable
     *    (liability_user_id) e incident_type (prueba de ruta, maniobra, otro).
     */
    public function up(): void
    {
        Schema::table('estimates', function (Blueprint $table) {
            $table->boolean('is_chargeable')->default(true)->after('total');
            $table->string('liability', 20)->nullable()->default('client')->after('is_chargeable');
            $table->unsignedBigInteger('liability_user_id')->nullable()->after('liability');
            $table->unsignedBigInteger('warranty_of_estimate_id')->nullable()->after('liability_user_id');
            $table->string('incident_type', 30)->nullable()->after('warranty_of_estimate_id');
            $table->timestamp('incident_reported_at')->nullable()->after('incident_type');

            $table->foreign('liability_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('warranty_of_estimate_id')->references('id')->on('estimates')->nullOnDelete();

            $table->index(['is_chargeable', 'liability']);
            $table->index('warranty_of_estimate_id');
        });
    }

    public function down(): void
    {
        Schema::table('estimates', function (Blueprint $table) {
            $table->dropForeign(['liability_user_id']);
            $table->dropForeign(['warranty_of_estimate_id']);
            $table->dropIndex(['is_chargeable', 'liability']);
            $table->dropIndex(['warranty_of_estimate_id']);
            $table->dropColumn([
                'is_chargeable',
                'liability',
                'liability_user_id',
                'warranty_of_estimate_id',
                'incident_type',
                'incident_reported_at',
            ]);
        });
    }
};
