<?php namespace Elphie\Guardian;

/**
 * Larvel 4 user management package. Extending the Auth module.
 * 
 * @package  Elphie
 * @subpackage Guardian
 * @author  Ahmad Shah Hafizan Hamidin <[ahmadshahhafizan[at]gmail.com]>
 * @license  MIT
 */

use Illuminate\Auth\AuthServiceProvider as ServiceProvider;

class GuardianServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('elphie/guardian', 'elphie/guardian');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerAuth();
	}

	public function registerAuth()
	{
		$this->app['auth'] = $this->app->share(function($app)
		{
			// Once the authentication service has actually been requested by the developer
			// we will set a variable in the application indicating such. This helps us
			// know that we need to set any queued cookies in the after event later.
			$app['auth.loaded'] = true;

			return new AuthManager($app);
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}