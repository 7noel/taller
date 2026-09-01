<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Gastos internos de la OT asumidos por el taller (responsabilidad propia):
     * arañazos, repuestos malogrados u otros errores durante el trabajo.
     * NO generan presupuesto ni factura: solo se registra el evento, el
     * responsable y el monto para reflejar la utilidad real de la OT.
     */
    public function up(): void
    {
        Schema::create('work_order_internal_expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');
            $table->string('type', 30); // scratch | damaged_part | other
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 3)->default('PEN');
            $table->decimal('exchange_rate', 12, 4)->default(1);
            $table->unsignedBigInteger('responsible_user_id')->nullable();
            $table->date('occurred_at')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('work_order_id')->references('id')->on('work_orders')->cascadeOnDelete();
            $table->foreign('responsible_user_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            $table->index(['work_order_id', 'type']);
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_order_internal_expenses');
    }
};
