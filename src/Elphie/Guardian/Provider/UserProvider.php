<?php namespace Elphie\Guardian\Provider;

/**
 * Laravel 4 user management package. Extending the Auth module.
 * 
 * @package  Elphie
 * @subpackage Guardian
 * @author  Ahmad Shah Hafizan Hamidin <[ahmadshahhafizan[at]gmail.com]>
 * @license  MIT
 */

use Elphie\Guardian\UserNotFoundException;

class UserProvider implements ProviderInterface {

	/**
	 * User eloquent model
	 * 
	 * @var Elphie\Guardian\Model\User
	 */
	protected $model = 'Elphie\Guardian\Model\User';

	/**
	 * Construct
	 * 
	 * @return void
	 */
	public function __construct($model = null)
	{
		if (isset($model))
		{
			$this->model = $model;
		}
	}

	/**
	 * Find user by primary key
	 * 
	 * @param  integer $id
	 * @return Elphie\Guardian\Model\User
	 */
	public function findById($id)
	{
		$model = $this->igniteModel();

		if ( ! $user = $model->newQuery()->find($id))
		{
			throw new UserNotFoundException("No user found with the ID [$id]");
		}

		return $user;
	}

	/**
	 * Find user by login attribute
	 * 
	 * @param  string $attribute
	 * @return Elphie\Guardian\Model\User
	 */
	public function findByLogin($attribute)
	{
		$model = $this->igniteModel();

		if ( ! $user = $model->newQuery()->where($model->getLoginAttribute(), '=', $attribute)->first())
		{
			throw new UserNotFoundException("No user found with the login value [$attribute]");
		}

		return $user;
	}

	/**
	 * Find user by activation code
	 * 
	 * @param  string $activationCode
	 * @return Elphie\Guardian\Model\User
	 */
	public function findByActivationCode($activationCode)
	{
		$model = $this->igniteModel();

		if ( ! $user = $model->newQuery()->where('activation_code', '=', $activationCode)->first())
		{
			throw new UserNotFoundException("No user found with the activation code [$activationCode]");
		}

		return $user;
	}

	/**
	 * Find all users
	 * 
	 * @return Elphie\Guardian\Model\User
	 */
	public function findAll()
	{
		$model = $this->igniteModel();

		return $model->newQuery()->get()->all();
	}

	/**
	 * Create new user
	 * 
	 * @param array $attributes
	 * @return Elphie\Guardian\Model\User
	 */
	public function create(array $attributes)
	{
		$model = $this->igniteModel();

		$model->fill($attributes);
		$model->save();

		return $model;
	}

	/**
	 * Instantiate user eloquent model
	 * 
	 * @return Elphie\Guardian\Model\User
	 */
	public function igniteModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
	}

}