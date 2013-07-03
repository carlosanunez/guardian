<?php namespace Elphie\Guardian\Model;

use Illuminate\Database\Eloquent\Model;
use Elphie\Guardian\GroupAlreadyExistsException;

class Group extends Model {

	/**
	 * [$table description]
	 * @var string
	 */
	protected $table = 'groups';

	/**
	 * [$timestamps description]
	 * @var boolean
	 */
	public $timestamps = false;

	/**
	 * [$fillable description]
	 * @var array
	 */
	protected $fillable = array('name', 'permissions');

	/**
	 * [setPermissionsAttribute description]
	 * @param [type] $value [description]
	 */
	public function setPermissionsAttribute($value)
	{
		if(is_array($value))
		{
			$this->attributes['permissions'] = json_encode($value);
		}
		else
		{
			json_decode($value);
			if (json_last_error() != JSON_ERROR_NONE) throw new \InvalidArgumentException('Permissions must be in array format');
			
			$this->attributes['permissions'] = $value;
		}
	}

	/**
	 * [users description]
	 * @return [type] [description]
	 */
	public function users()
	{
		return $this->hasMany('Elphie\Guardian\Model\UserGroup');
	}

	/**
	 * Save the model to the database.
	 *
	 * @param  array  $options
	 * @return bool
	 */
	public function save(array $options = array())
	{
		$query = $this->newQueryWithDeleted();

		// If the "saving" event returns false we'll bail out of the save and return
		// false, indicating that the save failed. This gives an opportunities to
		// listeners to cancel save operations if validations fail or whatever.
		if ($this->fireModelEvent('saving') === false)
		{
			return false;
		}

		// If the model already exists in the database we can just update our record
		// that is already in this database using the current IDs in this "where"
		// clause to only update this model. Otherwise, we'll just insert them.
		if ($this->exists)
		{
			if ( ! is_null(array_get($this->getDirty(), 'name')))
			{
				$name = array_get($this->getDirty(), 'name');

				if ( ! is_null($this->where('name', '=', $name)->first())) throw new GroupAlreadyExistsException("Group [{$name}] already exists");
			}

			$saved = $this->performUpdate($query);
		}

		// If the model is brand new, we'll insert it into our database and set the
		// ID attribute on the model to the value of the newly inserted row's ID
		// which is typically an auto-increment value managed by the database.
		else
		{
			if ( ! is_null(array_get($this->getDirty(), 'name')))
			{
				$name = array_get($this->getDirty(), 'name');

				if ( ! is_null($this->where('name', '=', $name)->first())) throw new GroupAlreadyExistsException("Group [{$name}] already exists");
			}

			$saved = $this->performInsert($query);
		}

		if ($saved) $this->finishSave($options);

		return $saved;
	}

	/**
	 * [delete description]
	 * @return [type] [description]
	 */
	public function delete()
	{
		foreach($this->users as $user)
		{
			return $user->delete();
		}

		if ($this->exists)
		{
			if ($this->fireModelEvent('deleting') === false) return false;

			// Here, we'll touch the owning models, verifying these timestamps get updated
			// for the models. This will allow any caching to get broken on the parents
			// by the timestamp. Then we will go ahead and delete the model instance.
			$this->touchOwners();

			$this->performDeleteOnModel();

			$this->exists = false;

			// Once the model has been deleted, we will fire off the deleted event so that
			// the developers may hook into post-delete operations. We will then return
			// a boolean true as the delete is presumably successful on the database.
			$this->fireModelEvent('deleted', false);

			return true;
		}
	}

}