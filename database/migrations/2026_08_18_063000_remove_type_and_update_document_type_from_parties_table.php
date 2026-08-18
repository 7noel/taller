<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Cambiar document_type de ENUM a string (idempotente)
        $columnType = DB::selectOne("SELECT DATA_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'parties' AND COLUMN_NAME = 'document_type'");
        if ($columnType && strtolower($columnType->DATA_TYPE) === 'enum') {
            Schema::table('parties', function (Blueprint $table) {
                $table->string('document_type', 10)->nullable()->change();
            });
        }

        // 2. Convertir datos existentes a códigos SUNAT (solo si aún son texto)
        DB::table('parties')->where('document_type', 'DNI')->update(['document_type' => '1']);
        DB::table('parties')->where('document_type', 'RUC')->update(['document_type' => '6']);
        DB::table('parties')->where('document_type', 'CEX')->update(['document_type' => '4']);
        DB::table('parties')->where('document_type', 'PAS')->update(['document_type' => '7']);

        // 3. Eliminar columna type si existe
        $typeExists = DB::selectOne("SELECT COUNT(*) as cnt FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'parties' AND COLUMN_NAME = 'type'");
        if ($typeExists && $typeExists->cnt > 0) {
            Schema::table('parties', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }

    public function down(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->enum('type', ['person', 'company'])->after('id');
            $table->enum('document_type', ['DNI', 'RUC', 'PAS', 'CEX'])->nullable()->change();
        });
    }
};