<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCheckitemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkitems', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->integer('itemcheck_group_id')->unsigned();
            $table->boolean('with_status')->default(true);
            $table->boolean('with_value')->default(false);
            $table->boolean('column_two')->default(false);
            $table->string('pre_value');
            $table->string('post_value');

            $table->foreign('itemcheck_group_id')->references('id')->on('checkitem_groups');
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
        Schema::drop('itemchecks');
    }
}
