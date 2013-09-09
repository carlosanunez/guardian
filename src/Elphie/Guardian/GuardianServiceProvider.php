<?php namespace Elphie\Guardian;

use Illuminate\Foundation\AliasLoader;
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
		$this->registerAuth();
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

	protected function registerAuth()
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

}