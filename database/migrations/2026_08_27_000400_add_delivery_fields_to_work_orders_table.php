<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->timestamp('delivered_at')->nullable()->after('estimated_end_date');
            $table->unsignedBigInteger('delivered_by')->nullable()->after('delivered_at');
            $table->timestamp('survey_sent_at')->nullable()->after('delivered_by');
            $table->string('survey_sent_to')->nullable()->after('survey_sent_at');
            $table->string('survey_sent_to_phone')->nullable()->after('survey_sent_to');
            $table->string('last_sent_to')->nullable()->after('survey_sent_to_phone');
            $table->string('last_sent_to_phone')->nullable()->after('last_sent_to');
            $table->timestamp('last_sent_at')->nullable()->after('last_sent_to_phone');

            $table->foreign('delivered_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropForeign(['delivered_by']);
            $table->dropColumn([
                'delivered_at',
                'delivered_by',
                'survey_sent_at',
                'survey_sent_to',
                'survey_sent_to_phone',
                'last_sent_to',
                'last_sent_to_phone',
                'last_sent_at',
            ]);
        });
    }
};
