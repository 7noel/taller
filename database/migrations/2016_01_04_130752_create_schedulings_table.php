<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSchedulingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedulings', function (Blueprint $table) {
            $table->increments('id');
            $table->time('time');
            $table->boolean('is_saturday');
            $table->boolean('a1');
            $table->boolean('a2');
            $table->boolean('a3');
            $table->boolean('a4');
            $table->boolean('a5');
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
        Schema::drop('scheduling');
    }
}
