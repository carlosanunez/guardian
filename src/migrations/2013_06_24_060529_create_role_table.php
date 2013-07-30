<?php

use Illuminate\Database\Migrations\Migration;

class CreateRoleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('groups', function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->text('permissions');
		});

		Schema::create('user_groups', function($table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('group_id');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('groups');
		Schema::drop('user_groups');
	}

}