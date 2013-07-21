<?php namespace Elphie\Guardian;

/**
 * Larvel 4 user management package. Extending the Auth module.
 * 
 * @package  Elphie
 * @subpackage Guardian
 * @author  Ahmad Shah Hafizan Hamidin <[ahmadshahhafizan[at]gmail.com]>
 * @license  MIT
 */

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
				if ( ! $user->isActivated()) throw new AccountNotActivatedException('This account is not activated');

				//deny login if account is suspended
				if ($user->isSuspended()) throw new AccountSuspendedException('This account is suspended');

				if ($login) $this->login($user, $remember);

				return true;
			}
		}

		throw new UserNotFoundException('User not found');
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
			throw new UserNotLoginException('Please log in to continue');
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
	 * Register new user
	 * 
	 * @param  array   $credentials
	 * @param  bool $activate
	 * @return Elphie\Guardian\Model\User
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
	 * Get user groups
	 * 
	 * @return array
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
	 * Determine if user belong to the given group
	 * 
	 * @param  string $group
	 * @return bool
	 */
	public function inGroup($group)
	{
		$groups = $this->groups();

		return in_array($group, $groups) ? true: false;
	}

	/**
	 * Determine if user has the permission
	 *
	 * @param  string $permission
	 * @return bool
	 */
	public function can($permission)
	{
		$permissions = $this->mergePermissions();

		return (array_key_exists($permission, $permissions) and $permissions[$permission]) ? true : false;
	}

	/**
	 * Determine if the user does not has the permission
	 * 
	 * @param  string $permission
	 * @return bool
	 */
	public function cannot($permission)
	{
		$permissions = $this->mergePermissions();

		if ( ! array_key_exists($permission, $permissions)) return true;
		return (array_key_exists($permission, $permissions) and ! $permissions[$permission]) ? true : false;
	}

	/**
	 * Add user to a group
	 * 
	 * @param  string $group
	 * @return bool
	 */
	public function attachGroup($name)
	{
		$groupProvider = $this->getGroupProvider();
		$group = $groupProvider->findByName($name);

		return $groupProvider->addUser($group->id, array($this->user()->id));
	}

	/**
	 * Remove user from a group
	 * 
	 * @param  string $group
	 * @return bool
	 */
	public function detachGroup($name)
	{
		$groupProvider = $this->getGroupProvider();
		$group = $groupProvider->findByName($name);

		return $groupProvider->removeUser($group->id, $this->user()->id);
	}

	/**
	 * Get user provider
	 * 
	 * @return void
	 */
	public function getUserProvider()
	{
		return new UserProvider;
	}

	/**
	 * Get group provider
	 * 
	 * @return void
	 */
	public function getGroupProvider()
	{
		return new GroupProvider;
	}

	/**
	 * Merge group permissions
	 * 
	 * @return array
	 */
	protected function mergePermissions()
	{
		$permissions = array();

		foreach ($this->user()->groups as $group)
		{
			$perms = json_decode($group->group->permissions, true);
			foreach ($perms as $key => $value)
			{
				$permissions[$key] = $value;
			}
		}

		return $permissions;
	}

}
