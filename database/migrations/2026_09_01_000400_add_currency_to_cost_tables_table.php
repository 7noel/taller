<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Soporte de moneda (PEN/USD) + snapshot del tipo de cambio para las tablas
     * de COSTOS que aún no lo tenían. Convención del sistema: exchange_rate =
     * soles por 1 dólar (PEN → 1). El cálculo de utilidad normaliza a PEN.
     */
    public function up(): void
    {
        Schema::table('service_vouchers', function (Blueprint $table) {
            $table->string('currency', 3)->default('PEN')->after('provider_id');
            $table->decimal('exchange_rate', 12, 4)->default(1)->after('currency');
        });

        Schema::table('third_party_orders', function (Blueprint $table) {
            $table->string('currency', 3)->default('PEN')->after('estimate_id');
            $table->decimal('exchange_rate', 12, 4)->default(1)->after('currency');
        });

        Schema::table('work_order_assignments', function (Blueprint $table) {
            $table->string('currency', 3)->default('PEN')->after('cost');
            $table->decimal('exchange_rate', 12, 4)->default(1)->after('currency');
        });
    }

    public function down(): void
    {
        Schema::table('service_vouchers', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate']);
        });

        Schema::table('third_party_orders', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate']);
        });

        Schema::table('work_order_assignments', function (Blueprint $table) {
            $table->dropColumn(['currency', 'exchange_rate']);
        });
    }
};
