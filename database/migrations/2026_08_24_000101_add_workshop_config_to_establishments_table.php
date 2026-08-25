<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('establishments', function (Blueprint $table) {
            $table->decimal('igv_rate', 5, 4)->default(0.1800)->after('email');
            $table->string('base_currency', 3)->default('PEN')->after('igv_rate');
            $table->boolean('prices_include_tax')->default(true)->after('base_currency');
            $table->decimal('default_hourly_rate', 12, 2)->default(0)->after('prices_include_tax');
            $table->decimal('default_panel_rate', 12, 2)->default(0)->after('default_hourly_rate');
        });
    }

    public function down(): void
    {
        Schema::table('establishments', function (Blueprint $table) {
            $table->dropColumn(['igv_rate', 'base_currency', 'prices_include_tax', 'default_hourly_rate', 'default_panel_rate']);
        });
    }
};