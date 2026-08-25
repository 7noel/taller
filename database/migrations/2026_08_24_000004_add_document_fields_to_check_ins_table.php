<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('check_ins', function (Blueprint $table) {
            $table->foreignId('document_series_id')->nullable()->after('establishment_id')->constrained('document_series');
            $table->string('document_number')->nullable()->after('document_series_id');
        });
    }

    public function down(): void
    {
        Schema::table('check_ins', function (Blueprint $table) {
            $table->dropConstrainedForeignId('document_series_id');
            $table->dropColumn('document_number');
        });
    }
};