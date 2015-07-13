<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceChecklistCheckitem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_checklist_checkitem', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_checklist_id');
            $table->integer('checkitem_id');
            $table->enum('status',['success', 'warning', 'danger', 'info'])->nullable()->default(null);
            $table->decimal('value',10,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('service_checklist_checkitem');
    }
}
