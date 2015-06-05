<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeaturesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('features', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('value');
			$table->integer('feature_group_id')->unsigned();
			$table->integer('catalog_car_id')->unsigned();
			
			$table->foreign('feature_group_id')->references('id')->on('feature_groups');
			$table->foreign('catalog_car_id')->references('id')->on('catalog_cars');
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
		Schema::drop('features');
	}

}
