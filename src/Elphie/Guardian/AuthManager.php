<?php namespace Elphie\Guardian;

/**
 * Laravel 4 user management package. Extending the Auth module.
 * 
 * @package  Elphie
 * @subpackage Guardian
 * @author  Ahmad Shah Hafizan Hamidin <[ahmadshahhafizan[at]gmail.com]>
 * @license  MIT
 */

class AuthManager extends \Illuminate\Auth\AuthManager {

	/**
	 * Create an instance of the database driver.
	 *
	 * @return \Elphie\Guardian\Guard
	 */
	protected function createDatabaseDriver()
	{
		$provider = $this->createDatabaseProvider();

		return new Guard($provider, $this->app['session']);
	}

	/**
	 * Create an instance of the Eloquent driver.
	 *
	 * @return \Elphie\Guardian\Guard
	 */
	public function createEloquentDriver()
	{
		$provider = $this->createEloquentProvider();

		return new Guard($provider, $this->app['session']);
	}

}