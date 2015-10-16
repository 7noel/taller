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
			$table->integer('job_id')->unsigned();
			$table->integer('gender');
			$table->string('address');
			$table->integer('ubigeo_id')->unsigned();
			$table->string('phone_personal');
			$table->string('phone_company');
			$table->string('mobile_personal');
			$table->string('mobile_company');
			$table->string('email_personal');
			$table->string('email_company');
			$table->integer('user_id')->unsigned()->nullable();
			$table->string('signature');
			$table->integer('other_id')->unsigned();

			$table->foreign('job_id')->references('id')->on('jobs');
			$table->foreign('id_type_id')->references('id')->on('id_types');
			$table->foreign('ubigeo_id')->references('id')->on('ubigeos');
			$table->foreign('user_id')->references('id')->on('users');

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
