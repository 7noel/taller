<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmployeesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('employees', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->string('paternal_surname');
			$table->string('maternal_surname');
			$table->string('full_name');
			$table->integer('id_type_id')->unsigned();
			$table->string('doc');
			$table->integer('gender');
			$table->string('address');
			$table->integer('ubigeo_id')->unsigned();
			$table->string('phone');
			$table->string('mobile');
			$table->string('email_personal');
			$table->string('email_company');

			$table->foreign('id_type_id')->references('id')->on('id_types');
			$table->foreign('ubigeo_id')->references('id')->on('ubigeos');

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
		Schema::drop('employees');
	}

}
