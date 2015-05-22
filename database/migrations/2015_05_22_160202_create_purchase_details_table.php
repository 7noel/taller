<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchaseDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('purchase_details', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('purchase_id')->unsigned();
			$table->integer('stock_id')->unsigned();
			$table->integer('unit_id')->unsigned();
			$table->decimal('price',15,2);
			$table->decimal('quantity',15,2);
			$table->decimal('discount',15,2);
			$table->decimal('total',15,2);

			$table->foreign('purchase_id')->references('id')->on('purchases');
			$table->foreign('stock_id')->references('id')->on('stocks');
			$table->foreign('unit_id')->references('id')->on('units');
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
		Schema::drop('purchase_details');
	}

}
