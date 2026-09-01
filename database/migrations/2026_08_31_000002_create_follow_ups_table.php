<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_ups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->nullable()->constrained('parties')->nullOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('estimate_id')->nullable()->constrained('estimates')->nullOnDelete();
            $table->date('date');
            $table->string('type', 20)->default('call');
            $table->text('notes')->nullable();
            $table->date('next_action_date')->nullable();
            $table->boolean('done')->default(false);
            $table->dateTime('done_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['party_id', 'date']);
            $table->index(['vehicle_id', 'date']);
            $table->index('next_action_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_ups');
    }
};
