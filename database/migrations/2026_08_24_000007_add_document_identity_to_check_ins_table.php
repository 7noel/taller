<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('check_ins', function (Blueprint $table) {
            $table->string('document_type_code')->nullable()->after('document_series_id');
            $table->string('document_serie')->nullable()->after('document_type_code');
            $table->string('document_sn')->nullable()->after('document_number');
        });

        // Backfill: rellena las columnas snapshot desde document_series + document_types.
        DB::statement('
            UPDATE check_ins ci
            INNER JOIN document_series ds ON ds.id = ci.document_series_id
            INNER JOIN document_types dt ON dt.id = ds.document_type_id
            SET
                ci.document_type_code = dt.code,
                ci.document_serie = ds.prefix_serie,
                ci.document_sn = CONCAT(ds.prefix_serie, \'-\', LPAD(IFNULL(ci.document_number, 0), 6, \'0\'))
        ');

        Schema::table('check_ins', function (Blueprint $table) {
            $table->index('document_sn');
            $table->index('document_serie');
            $table->index('document_number');
        });
    }

    public function down(): void
    {
        Schema::table('check_ins', function (Blueprint $table) {
            $table->dropIndex(['document_sn']);
            $table->dropIndex(['document_serie']);
            $table->dropIndex(['document_number']);
            $table->dropColumn(['document_type_code', 'document_serie', 'document_sn']);
        });
    }
};