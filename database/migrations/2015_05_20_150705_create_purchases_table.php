<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePurchasesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('purchases', function(Blueprint $table)
		{
			$table->increments('id');
			$table->date('date');
			$table->integer('document_type_id')->unsigned();
			$table->integer('series');
			$table->integer('number');
			$table->date('dispatch_note_date');
			$table->integer('dispatch_note_series');
			$table->integer('dispatch_note_number');
			$table->integer('company_id')->unsigned();
			$table->integer('payment_condition_id')->unsigned();
			$table->date('due_date');
			$table->integer('currency_id')->unsigned();
			$table->decimal('subtotal',14,2);
			$table->decimal('igv',14,2);
			$table->decimal('total',14,2);

			$table->foreign('document_type_id')->references('id')->on('document_types');
			$table->foreign('company_id')->references('id')->on('companies');
			$table->foreign('payment_condition_id')->references('id')->on('payment_conditions');
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
		Schema::drop('purchases');
	}

}
