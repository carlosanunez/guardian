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
		Schema::create(Config::get('elphie/guardian::group_table'), function($table)
		{
			$table->increments('id');
			$table->string('name');
			$table->text('permissions');
		});

		Schema::create(Config::get('elphie/guardian::user_group_table'), function($table)
		{
			$table->increments('id');
			$table->integer('user_id');
			$table->integer('group_id');
		});

		$group = Config::get('elphie/guardian::default.group');
		$groupProvider = Auth::getGroupProvider();
		$defaultgroup = $groupProvider->create($group);

		$userProvider = Auth::getUserProvider();
		$defaultUser = Auth::findByLogin(Config::get('elphie/guardian::default.user.email'));
		$groupProvider->addUser($defaultgroup->id, array($defaultUser->id));
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop(Config::get('elphie/guardian::group_table'));
		Schema::drop(Config::get('elphie/guardian::user_group_table'));
	}

}