<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCarQuotesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('car_quotes', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('catalog_car_id')->unsigned();
			$table->decimal('price', 15, 2);
			$table->decimal('set_price', 15, 2);
			
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
		Schema::drop('car_quotes');
	}

}
