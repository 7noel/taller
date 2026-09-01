<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estimates', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_estimate_id')->nullable()->after('work_order_id');

            $table->foreign('parent_estimate_id')->references('id')->on('estimates')->nullOnDelete();
            $table->index('parent_estimate_id');
        });
    }

    public function down(): void
    {
        Schema::table('estimates', function (Blueprint $table) {
            $table->dropForeign(['parent_estimate_id']);
            $table->dropIndex(['parent_estimate_id']);
            $table->dropColumn('parent_estimate_id');
        });
    }
};
