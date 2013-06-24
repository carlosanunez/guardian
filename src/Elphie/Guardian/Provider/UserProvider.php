<?php namespace Elphie\Guardian\Provider;

use Elphie\Guardian\UserNotFoundException;

class UserProvider implements ProviderInterface {

	protected $model = 'Elphie\Guardian\Model\User';

	public function __construct($model = null)
	{
		if (isset($model))
		{
			$this->model = $model;
		}
	}

	public function findById($id)
	{
		$model = $this->igniteModel();

		if ( ! $user = $model->newQuery()->find($id))
		{
			throw new UserNotFoundException("No user found with the ID [$id]");
		}

		return $user;
	}

	public function findByLogin($attribute)
	{
		$model = $this->igniteModel();

		if ( ! $user = $model->newQuery()->where($model->getLoginAttribute(), '=', $attribute)->first())
		{
			throw new UserNotFoundException("No user found with the login value [$attribute]");
		}

		return $user;
	}

	public function findByActivationCode($activationCode)
	{
		$model = $this->igniteModel();

		if ( ! $user = $model->newQuery()->where('activation_code', '=', $activationCode)->first())
		{
			throw new UserNotFoundException("No user found with the activation code [$activationCode]");
		}

		return $user;
	}

	public function findAll()
	{
		$model = $this->igniteModel();

		return $model->newQuery()->get()->all();
	}

	public function create(array $attributes)
	{
		$model = $this->igniteModel();

		$model->fill($attributes);
		$model->save();

		return $model;
	}

	public function igniteModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
	}

}