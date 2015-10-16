<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateServiceChecklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('service_checklists', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('order_id')->unsigned();
            $table->string('company_name');
            $table->string('plate');
            $table->date('date');
            $table->string('observation');
            $table->string('adviser_id');
            $table->string('technician_id');
            $table->enum('status',['process', 'inspect', 'rework', 'approved', 'printed'])->default('process');
            $table->string('created_by_id');
            $table->string('updated_by_id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('service_checklists');
    }
}
