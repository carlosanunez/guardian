<?php namespace Elphie\Guardian\Provider;

use Elphie\Guardian\GroupNotFoundException;
use Elphie\Guardian\GroupAlreadyExistsException;

class GroupProvider implements ProviderInterface {

	protected $model = 'Elphie\Guardian\Model\Group';

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

		if ( ! $group = $model->newQuery()->find($id))
		{
			throw new GroupNotFoundException("No group found with the ID [$id]");
		}

		return $group;
	}

	public function findByName($group)
	{
		$model = $this->igniteModel();

		if ( ! $group = $model->newQuery()->where('name', '=', $group)->first())
		{
			throw new GroupNotFoundException("No group found with the name [{$group}]");
		}

		return $group;
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

	public function getPermissions($name)
	{
		$group = $this->findByName($name);

		return $group->permissions != '' ? json_decode($group->permissions) : null;
	}

	public function igniteModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
	}

}