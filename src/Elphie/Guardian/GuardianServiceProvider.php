<?php namespace Elphie\Guardian;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;

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
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('elphie.guardian.user');
	}

}