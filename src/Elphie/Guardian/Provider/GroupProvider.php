<?php namespace Elphie\Guardian\Provider;

/**
 * Laravel 4 user management package. Extending the Auth module.
 * 
 * @package  Elphie
 * @subpackage Guardian
 * @author  Ahmad Shah Hafizan Hamidin <[ahmadshahhafizan[at]gmail.com]>
 * @license  MIT
 */

use Elphie\Guardian\Model\UserGroup;
use Elphie\Guardian\GroupNotFoundException;
use Elphie\Guardian\GroupAlreadyExistsException;

class GroupProvider implements ProviderInterface {

	/**
	 * Group eloquent model
	 * 
	 * @var Elphie\Guardian\Model\Group
	 */
	protected $model = 'Elphie\Guardian\Model\Group';

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
	 * Find group by primary key
	 * 
	 * @param  integer $id
	 * @return Elphie\Guardian\Model\Group
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
	 * Find group by name
	 * 
	 * @param  string $group
	 * @return Elphie\Guardian\Model\Group
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
	 * Find all groups
	 * 
	 * @return Elphie\Guardian\Model\Group
	 */
	public function findAll()
	{
		$model = $this->igniteModel();

		return $model->newQuery()->get()->all();
	}

	/**
	 * Create new group
	 * 
	 * @param  array  $attributes
	 * @return Elphie\Guardian\Model\Group
	 */
	public function create(array $attributes)
	{
		$model = $this->igniteModel();

		$model->fill($attributes);
		$model->save();

		return $model;
	}

	/**
	 * Get group permissions
	 * 
	 * @param  string $name
	 * @return array|null
	 */
	public function getPermissions($name)
	{
		$group = $this->findByName($name);

		return $group->permissions != '' ? json_decode($group->permissions) : null;
	}

	/**
	 * Get user from the group provided
	 * 
	 * @param  string $name
	 * @return Elphie\Guardian\Model\UserGroup
	 */
	public function getUsers($name)
	{
		$group = $this->findByName($name);
		
		return $group->users;
	}

	/**
	 * Add users to a group
	 *
	 * @var integer $group
	 * @var array
	 * @return  bool
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
	 * Remove user from a group
	 * 
	 * @param  integer $group
	 * @param  integer $user
	 * @return bool
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
	 * Instantiate group eloquent model
	 * 
	 * @return Elphie\Guardian\Model\Group
	 */
	public function igniteModel()
	{
		$class = '\\'.ltrim($this->model, '\\');

		return new $class;
	}

}