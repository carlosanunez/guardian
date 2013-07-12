<?php namespace Elphie\Guardian\Provider;

use Elphie\Guardian\Model\UserGroup;
use Elphie\Guardian\GroupNotFoundException;
use Elphie\Guardian\GroupAlreadyExistsException;

class GroupProvider implements ProviderInterface {

	/**
	 * [$model description]
	 * @var string
	 */
	protected $model = 'Elphie\Guardian\Model\Group';

	/**
	 * [__construct description]
	 * @param [type] $model [description]
	 */
	public function __construct($model = null)
	{
		if (isset($model))
		{
			$this->model = $model;
		}
	}

	/**
	 * [findById description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function findById($id)
	{
		$model = $this->igniteModel();

		if ( ! $group = $model->newQuery()->find($id))
		{
			throw new GroupNotFoundException("No group found with the ID [$id]");
		}

		return $group;
	}

	/**
	 * [findByName description]
	 * @param  [type] $group [description]
	 * @return [type]        [description]
	 */
	public function findByName($group)
	{
		$model = $this->igniteModel();

		if ( ! $group = $model->newQuery()->where('name', '=', $group)->first())
		{
			throw new GroupNotFoundException("No group found with the name [{$group}]");
		}

		return $group;
	}

	/**
	 * [findAll description]
	 * @return [type] [description]
	 */
	public function findAll()
	{
		$model = $this->igniteModel();

		return $model->newQuery()->get()->all();
	}

	/**
	 * [create description]
	 * @param  array  $attributes [description]
	 * @return [type]             [description]
	 */
	public function create(array $attributes)
	{
		$model = $this->igniteModel();

		$model->fill($attributes);
		$model->save();

		return $model;
	}

	/**
	 * [getPermissions description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function getPermissions($name)
	{
		$group = $this->findByName($name);

		return $group->permissions != '' ? json_decode($group->permissions) : null;
	}

	/**
	 * [getUsers description]
	 * @param  [type] $name [description]
	 * @return [type]       [description]
	 */
	public function getUsers($name)
	{
		$group = $this->findByName($name);
		
		return $group->users;
	}

	/**
	 * [$users description]
	 * @var array
	 */
	public function addUser($group, array $users)
	{
		foreach ($users as $user)
		{
			UserGroup::create(array('user_id' => $user, 'group_id' => $group));
		}

		return true;
	}

	/**
	 * [removeUser description]
	 * @param  [type] $group [description]
	 * @param  [type] $user  [description]
	 * @return [type]        [description]
	 */
	public function removeUser($group, $user)
	{
		$userGroup = UserGroup::where('user_id', '=', $user)
		->where('group_id', '=', $group)
		->first();

		$userGroup->delete();

		return true;
	}

	/**
	 * [igniteModel description]
	 * @return [type] [description]
	 */
	public function igniteModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
	}

}