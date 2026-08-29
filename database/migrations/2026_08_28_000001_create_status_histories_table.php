<?php

use App\Models\Estimate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_histories', function (Blueprint $table) {
            $table->id();
            $table->morphs('subject');
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('actor_type')->default('internal');
            $table->text('comments')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });

        // Migrar el historial existente de presupuestos a la tabla genérica.
        DB::table('status_histories')->insertUsing(
            ['subject_type', 'subject_id', 'from_status', 'to_status', 'user_id', 'actor_type', 'comments', 'created_at', 'updated_at'],
            DB::table('estimate_status_history')
                ->selectRaw('? as subject_type, estimate_id as subject_id, from_status, to_status, user_id, ? as actor_type, comments, created_at, updated_at', [Estimate::class, 'internal'])
        );

        Schema::dropIfExists('estimate_status_history');
    }

    public function down(): void
    {
        Schema::create('estimate_status_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('estimate_id');
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('comments')->nullable();
            $table->timestamps();

            $table->foreign('estimate_id')->references('id')->on('estimates')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->index('estimate_id');
        });

        DB::table('estimate_status_history')->insertUsing(
            ['estimate_id', 'from_status', 'to_status', 'user_id', 'comments', 'created_at', 'updated_at'],
            DB::table('status_histories')
                ->where('subject_type', Estimate::class)
                ->selectRaw('subject_id as estimate_id, from_status, to_status, user_id, comments, created_at, updated_at')
        );

        Schema::dropIfExists('status_histories');
    }
};
