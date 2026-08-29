<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Una OT agrupa uno o más presupuestos aprobados (muchos a uno).
        // La misma OT puede recibir presupuestos de distintos check-ins (reingresos).
        Schema::table('estimates', function (Blueprint $table) {
            $table->unsignedBigInteger('work_order_id')->nullable()->after('status');
            $table->foreign('work_order_id')->references('id')->on('work_orders')->nullOnDelete();
            $table->index('work_order_id');
        });

        // Vincula cada visita física (check-in original y reingresos) a la OT.
        Schema::table('check_ins', function (Blueprint $table) {
            $table->unsignedBigInteger('work_order_id')->nullable()->after('status');
            $table->foreign('work_order_id')->references('id')->on('work_orders')->nullOnDelete();
            $table->index('work_order_id');
        });
    }

    public function down(): void
    {
        Schema::table('check_ins', function (Blueprint $table) {
            $table->dropForeign(['work_order_id']);
            $table->dropIndex(['work_order_id']);
            $table->dropColumn('work_order_id');
        });

        Schema::table('estimates', function (Blueprint $table) {
            $table->dropForeign(['work_order_id']);
            $table->dropIndex(['work_order_id']);
            $table->dropColumn('work_order_id');
        });
    }
};
