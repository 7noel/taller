<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('establishments', function (Blueprint $table) {
            $table->string('celular')->nullable()->after('phone');
            $table->string('ubigeo_code', 6)->nullable()->after('address');
            $table->foreign('ubigeo_code')->references('code')->on('ubigeos')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('establishments', function (Blueprint $table) {
            $table->dropForeign(['ubigeo_code']);
            $table->dropColumn(['celular', 'ubigeo_code']);
        });
    }
};