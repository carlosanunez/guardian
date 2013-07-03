<?php namespace Elphie\Guardian;

use Illuminate\Auth\UserInterface;
use Elphie\Guardian\Model\User;
use Elphie\Guardian\Provider\UserProvider;
use Elphie\Guardian\Provider\GroupProvider;
use Elphie\Guardian\AccountNotActivatedException;
use Elphie\Guardian\AccountSuspendedException;
use Elphie\Guardian\UserNotFoundException;
use Elphie\Guardian\UserNotLoginException;
use Carbon\Carbon;

class Guard extends \Illuminate\Auth\Guard {

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
				if ( ! $user->isActivated()) throw new AccountNotActivated('This account is not activated');

				//deny login if account is suspended
				if ($user->isSuspended()) throw new AccountSuspended('This account is suspended');

				if ($login) $this->login($user, $remember);

				return true;
			}
		}

		throw new UserNotFound('User not found');
	}

	/**
	 * Log the user out of the application.
	 *
	 * @return void
	 */
	public function logout()
	{
		if (is_null($this->user()))
		{
			throw new UserNotLogin('Please log in to continue');
		}
		else
		{
			//record last login
			$this->user()->last_login = Carbon::now()->toDateTimeString();
			$this->user()->save();
		}

		parent::logout();
	}

	/**
	 * [register description]
	 * @param  array   $credentials [description]
	 * @param  boolean $activate    [description]
	 * @return [type]               [description]
	 */
	public function register(array $credentials, $activate = false)
	{
		$user = new User;
		$user->fill($credentials);
		$user->save();

		if ($activate)
		{
			$user->activateAccount($user->getActivationCode());
		}

		return $user;
	}

	/**
	 * [groups description]
	 * @return [type] [description]
	 */
	public function groups()
	{
		$groups = array();

		foreach ($this->user()->groups as $group)
		{
			array_push($groups, $group->group->name);
		}

		return $groups;
	}

	/**
	 * [inGroup description]
	 * @param  [type] $group [description]
	 * @return [type]        [description]
	 */
	public function inGroup($group)
	{
		$groups = $this->groups();

		return in_array($group, $groups) ? true: false;
	}

	public function can()
	{

	}

	/**
	 * [getUserProvider description]
	 * @return [type] [description]
	 */
	public function getUserProvider()
	{
		return new UserProvider;
	}

	/**
	 * [getGroupProvider description]
	 * @return [type] [description]
	 */
	public function getGroupProvider()
	{
		return new GroupProvider;
	}

}
