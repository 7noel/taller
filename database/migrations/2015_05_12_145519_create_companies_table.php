<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompaniesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('companies', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('company_name');
			$table->string('brand_name');
			$table->string('name');
			$table->string('paternal_surname');
			$table->string('maternal_surname');
			$table->integer('id_type_id')->unsigned();
			$table->string('doc');
			$table->string('address');
			$table->integer('ubigeo_id')->unsigned();
			$table->string('phone');
			$table->string('mobile');
			$table->string('email');
			$table->date('birth')->nullable();
			$table->boolean('is_provider');

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
		Schema::drop('companies');
	}

}
