<?php namespace Elphie\Guardian;

use Carbon\Carbon;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Auth\UserInterface;
use Elphie\Guardian\UserNotLoginException;
use Illuminate\Auth\UserProviderInterface;
use Elphie\Guardian\UserNotFoundException;
use Illuminate\Session\Store as SessionStore;
use Elphie\Guardian\AccountSuspendedException;
use Elphie\Guardian\AccountIsActivatedException;
use Elphie\Guardian\AccountNotActivatedException;
use Elphie\Guardian\InvalidActivationCodeException;
use Elphie\Guardian\Contracts\UserRepositoryInterface;

class Guard extends \Illuminate\Auth\Guard {

    protected $userRepo;

    protected $config;

    /**
     * Create a new authentication guard.
     *
     * @param  \Illuminate\Auth\UserProviderInterface  $provider
     * @param  \Illuminate\Session\Store  $session
     * @param  Elphie\Guardian\Contracts\UserRepositoryInterface  $user
     * @return void
     */
    public function __construct(UserProviderInterface $provider, SessionStore $session, ConfigRepository $config, UserRepositoryInterface $user)
    {
        $this->session = $session;
        $this->provider = $provider;
        $this->config = $config;
        $this->userRepo = $user;
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param  array  $credentials
     * @param  bool   $remember
     * @param  bool   $login
     * @return bool
     * @throws Elphie\Guardian\AccountNotActivated
     * @throws Elphie\Guardian\AccountSuspended
     * @throws Elphie\Guardian\UserNotFound
     */
    public function attempt(array $credentials = array(), $remember = false, $login = true)
    {
        $this->fireAttemptEvent($credentials, $remember, $login);

        $user = $this->provider->retrieveByCredentials($credentials);

        // If an implementation of UserInterface was returned, we'll ask the provider
        // to validate the user against the given credentials, and if they are in
        // fact valid we'll log the users into the application and return true.
        if ($user instanceof UserInterface)
        {
            if ($this->provider->validateCredentials($user, $credentials))
            {
                //deny login if account is not activated
                if ( ! $user->isActivated()) throw new AccountNotActivatedException('This account is not activated');

                //deny login if account is suspended
                if ($user->isSuspended()) throw new AccountSuspendedException('This account is suspended');

                if ($login) $this->login($user, $remember);

                return true;
            }
        }

        throw new UserNotFoundException('User not found');
    }

    public function logout()
    {
        if (is_null($this->user())) throw new UserNotLoginException('Please log in to continue');

        $this->user()->last_login = Carbon::now();
        $this->user()->save();

        parent::logout();
    }

    public function register()
    {
        $this->events->fire('elphie.guardian.register');
    }

    public function activateAccount($activationCode)
    {
        //Throw an exception if user already activated
        if ($this->user()->activated) throw new AccountIsActivatedException('Account already activated');
        //Throw an exception if user provide invalid activation code
        if ($activationCode != $this->user()->getActivationCode()) throw new InvalidActivationCodeException("Invalid activation code [$activationCode]");

        $this->user()->activated = 1;
        $this->user()->save();

        //Fire elphie guardian account activation event
        $this->events->fire('elphie.guardian.activation');

        return true;
    }

    public function resetPassword()
    {
        $this->events->fire('elphie.guardian.resetpassword');
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getUserRepository()
    {
        return $this->userRepo;
    }

}


