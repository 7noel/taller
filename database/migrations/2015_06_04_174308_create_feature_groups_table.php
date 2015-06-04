<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFeatureGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feature_groups', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name');
			$table->enum('template', ['primaryLeft', 'primaryRight', 'in', 'out']);
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
		Schema::drop('feature_groups');
	}

}
