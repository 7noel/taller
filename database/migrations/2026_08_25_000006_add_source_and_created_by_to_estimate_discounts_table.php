<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('estimate_discounts', function (Blueprint $table) {
            $table->string('source', 30)->default('other')->after('type');
            $table->unsignedBigInteger('created_by')->nullable()->after('applied_to');

            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();

            $table->index('source');
        });
    }

    public function down(): void
    {
        Schema::table('estimate_discounts', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropIndex(['source']);
            $table->dropColumn(['source', 'created_by']);
        });
    }
};