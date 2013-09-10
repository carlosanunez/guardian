<?php namespace Elphie\Guardian;

use Illuminate\Foundation\AliasLoader;
//use Illuminate\Auth\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\ServiceProvider;

class GuardianServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('elphie/guardian', 'elphie/guardian');

		$loader = AliasLoader::getInstance();
		$loader->alias('Guardian\User', 'Elphie\Guardian\Facades\User');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerRepositories();
		$this->registerFacades();
		$this->registerAuthManager();
		$this->registerAuthEvents();
	}

	protected function registerRepositories()
	{
		$this->app->singleton('Elphie\Guardian\Contracts\UserRepositoryInterface', function($app)
		{
			return new Repositories\UserRepository($app);
		});
	}

	protected function registerFacades()
	{
		$this->app['elphie.guardian.user'] = $this->app->share(function($app)
		{
			return $app->make('Elphie\Guardian\Contracts\UserRepositoryInterface');
		});
	}


	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function registerAuthManager()
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
	 * Register the events needed for authentication.
	 *
	 * @return void
	 */
	protected function registerAuthEvents()
	{
		$app = $this->app;

		$app->after(function($request, $response) use ($app)
		{
			// If the authentication service has been used, we'll check for any cookies
			// that may be queued by the service. These cookies are all queued until
			// they are attached onto Response objects at the end of the requests.
			if (isset($app['auth.loaded']))
			{
				foreach ($app['auth']->getDrivers() as $driver)
				{
					foreach ($driver->getQueuedCookies() as $cookie)
					{
						$response->headers->setCookie($cookie);
					}
				}
			}
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('auth', 'elphie.guardian.user');
	}

}