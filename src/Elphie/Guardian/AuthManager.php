<?php namespace Elphie\Guardian;

class AuthManager extends \Illuminate\Auth\AuthManager {

    /**
     * Create an instance of the database driver.
     *
     * @return \Elphie\Guardian\Guard
     */
    protected function createDatabaseDriver()
    {
        $provider = $this->createDatabaseProvider();

        return new Guard($provider, $this->app['session'], $this->app->make('Elphie\Guardian\Contracts\UserRepositoryInterface'));
    }

    /**
     * Create an instance of the Eloquent driver.
     *
     * @return \Elphie\Guardian\Guard
     */
    public function createEloquentDriver()
    {
        $provider = $this->createEloquentProvider();

        return new Guard($provider, $this->app['session'], $this->app->make('Elphie\Guardian\Contracts\UserRepositoryInterface'));
    }

}