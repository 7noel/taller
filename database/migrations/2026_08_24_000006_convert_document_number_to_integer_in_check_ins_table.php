<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Columna temporal entera
        Schema::table('check_ins', function (Blueprint $table) {
            $table->unsignedInteger('document_number_int')->nullable()->after('document_series_id');
        });

        // 2. Backfill: extrae el correlativo entero de "IV01-00001" → 1
        DB::table('check_ins')
            ->whereNotNull('document_number')
            ->where('document_number', 'REGEXP', '^[A-Z0-9]+-[0-9]+$')
            ->update([
                'document_number_int' => DB::raw("CAST(SUBSTRING_INDEX(document_number, '-', -1) AS UNSIGNED)"),
            ]);

        // 3. Elimina la columna string y renombra la entera
        Schema::table('check_ins', function (Blueprint $table) {
            $table->dropColumn('document_number');
        });

        Schema::table('check_ins', function (Blueprint $table) {
            $table->renameColumn('document_number_int', 'document_number');
        });
    }

    public function down(): void
    {
        Schema::table('check_ins', function (Blueprint $table) {
            $table->string('document_number')->nullable()->after('document_series_id');
        });
    }
};