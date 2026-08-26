<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('public_approval_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->enum('action', ['approved', 'rejected'])->default('approved');
            $table->string('entity_type'); // check_in | estimate
            $table->unsignedBigInteger('entity_id');
            $table->enum('actor_type', ['internal', 'portal'])->default('portal');
            $table->unsignedBigInteger('actor_user_id')->nullable();
            $table->string('actor_recipient')->nullable(); // snapshot del destinatario (portal)
            $table->string('actor_phone')->nullable();
            $table->text('reason')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->foreign('vehicle_id')->references('id')->on('vehicles')->cascadeOnDelete();
            $table->foreign('actor_user_id')->references('id')->on('users')->nullOnDelete();

            $table->index('vehicle_id');
            $table->index(['entity_type', 'entity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('public_approval_logs');
    }
};
