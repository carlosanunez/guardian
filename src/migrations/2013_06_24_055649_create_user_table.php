<?php

use Illuminate\Database\Migrations\Migration;

class CreateUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create(Config::get('elphie/guardian::user_table'), function($table)
		{
			$table->increments('id');
			$table->string('email', 150);
			$table->string('nickname')->nullable();
			$table->string('password', 60);
			$table->string('first_name')->nullable();
			$table->string('last_name')->nullable();
			$table->string('activation_code')->nullable();
			$table->boolean('activated')->default(0);
			$table->dateTime('activated_at')->default('0000-00-00 00:00:00');
			$table->string('reset_password_code')->nullable();
			$table->dateTime('last_login')->default('0000-00-00 00:00:00');
			$table->boolean('suspended')->default(0);
			$table->softDeletes();
			$table->timestamps();
		});

		Schema::create(Config::get('elphie/guardian::metadata_table'), function($table)
		{
			$table->increments('id');
			$table->integer('user_id')->unsigned();
			$table->string('key');
			$table->string('value');

			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});

		$user = Config::get('elphie/guardian::default.user');
		Auth::register($user, true);
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop(Config::get('elphie/guardian::metadata_table'));
		Schema::drop(Config::get('elphie/guardian::user_table'));
	}

}