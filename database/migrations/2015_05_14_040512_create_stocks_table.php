<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStocksTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stocks', function(Blueprint $table)
		{
			$table->increments('id');

			$table->integer('warehouse_id')->unsigned();
			$table->integer('product_id')->unsigned();
			$table->decimal('stock',15,4);
			$table->integer('currency_id')->unsigned()->default(1);
			$table->decimal('avarage_value');

			$table->foreign('warehouse_id')->references('id')->on('warehouses');
			$table->foreign('product_id')->references('id')->on('products');
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
		Schema::drop('stocks');
	}

}
