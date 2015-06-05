<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCatalogCarsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('catalog_cars', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('manufacture_year')->unsigned();
			$table->integer('model_year')->unsigned();
			$table->string('cylinder');
			$table->string('transmission');
			$table->string('seats');
			$table->string('fuel');
			$table->integer('version_id')->unsigned();
			$table->integer('currency_id')->unsigned();
			$table->decimal('price', 15, 2);
			
			$table->foreign('version_id')->references('id')->on('versions');
			$table->foreign('currency_id')->references('id')->on('currencies');
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
		Schema::drop('catalog_cars');
	}

}
