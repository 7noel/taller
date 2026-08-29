<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('part_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('part_id');
            $table->unsignedBigInteger('estimate_id')->nullable();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->decimal('quantity', 12, 2)->default(1);
            $table->enum('status', ['pending', 'ordered', 'in_transit', 'received'])->default('pending');
            $table->date('ordered_at')->nullable();
            $table->date('expected_delivery')->nullable();
            $table->date('delivered_at')->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('part_id')->references('id')->on('parts')->cascadeOnDelete();
            // Nota: la FK de estimate_id se agrega en la migración
            // 2026_08_28_000300 (estimates se crea después de part_orders).
            $table->foreign('provider_id')->references('id')->on('parties')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();

            $table->index('status');
            $table->index('provider_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('part_orders');
    }
};