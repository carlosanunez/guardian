<?php namespace Elphie\Guardian\Model;

/**
 * Laravel 4 user management package. Extending the Auth module.
 * 
 * @package  Elphie
 * @subpackage Guardian
 * @author  Ahmad Shah Hafizan Hamidin <[ahmadshahhafizan[at]gmail.com]>
 * @license  MIT
 */

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Elphie\Guardian\UserIsActivatedException;
use Carbon\Carbon;

class User extends Model implements UserInterface, RemindableInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * Soft delete user
	 *
	 * @var  bool
	 */
	protected $softDelete = true;

	/**
	 * [$loginAttribute description]
	 * @var string
	 */
	protected $loginAttribute = 'email';

	protected $userMetadataModel = 'Elphie\Guardian\Model\UserMetadata';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'deleted_at', 'activation_code');

	/**
	 * [$guarded description]
	 * @var array
	 */
	protected $guarded = array('activation_code', 'reset_password_code');

	/**
	 * [$fillable description]
	 * @var array
	 */
	protected $fillable = array('email', 'password', 'nickname', 'first_name', 'last_name', 'activated');

	public function metadata()
	{
		return $this->hasMany('Elphie\Guardian\Model\UserMetadata');
	}

	public function groups()
	{
		return $this->hasMany('Elphie\Guardian\Model\UserGroup');
	}

	/**
	 * [setPasswordAttribute description]
	 * @param [type] $value [description]
	 */
	public function setPasswordAttribute($value)
	{
		$this->attributes['password'] = Hash::make($value);
	}

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	public function getMetadata($key = null)
	{
		$metadatas = array();

		foreach ($this->metadata as $m)
		{
			$metadatas[$m->key] = is_null(json_decode($m->value, true)) ? $m->value : json_decode($m->value, true);
		}

		if ( ! is_null($key))
		{
			return array_get($metadatas, $key);
		}

		return $metadatas;
	}

	/**
	 * [setMetadata description]
	 * @param [type] $key   [description]
	 * @param [type] $value [description]
	 */
	public function setMetadata($key, $value)
	{
		$model = $this->igniteModel();
		$parentKey = str_contains($key, '.') ? explode('.', $key) : (array) $key;
		$parent = $this->getMetadata($parentKey[0]);

		if ( ! is_null($parent))
		{
			if (count($parentKey) > 1)
			{
				array_set($parent, str_replace($parentKey[0].'.', '', $key), $value);
				$model->newQuery()->where('user_id', '=', $this->id)->where('key', '=', $parentKey[0])->update(array('value' => json_encode($parent)));
			}
			else
			{
				$model->newQuery()->where('user_id', '=', $this->id)->where('key', '=', $parentKey[0])->update(array('value' => json_encode($value)));
			}
		}
		else
		{
			$model->newQuery()->insert(array(
				'user_id' => $this->id,
				'key' => $parentKey[0],
				'value' => json_encode($value)
			));
		}

		return true;
	}

	public function attachMetadata(array $metadatas)
	{
		foreach ($metadatas as $key => $value)
		{
			$this->setMetadata($key, $value);
		}

		return true;
	}

	/**
	 * [getLoginAttribute description]
	 * @return [type] [description]
	 */
	public function getLoginAttribute()
	{
		return $this->loginAttribute;
	}

	/**
	 * [getActivationCode description]
	 * @return [type] [description]
	 */
	public function getActivationCode()
	{
		$this->activation_code = $activationCode = Str::random(40);

		$this->save();

		return $activationCode;
	}

	/**
	 * [activateAccount description]
	 * @param  [type] $activationCode [description]
	 * @return [type]                 [description]
	 */
	public function activateAccount($activationCode)
	{
		if ($this->activated) throw new UserIsActivatedException('This account is already activated');

		if ($this->activation_code != $activationCode) return false;

		$this->activation_code = null;
		$this->activated = 1;
		$this->activated_at = Carbon::now()->toDateTimeString();
		$this->save();

		return true;
	}

	/**
	 * [isActivated description]
	 * @return boolean [description]
	 */
	public function isActivated()
	{
		return (bool) $this->activated;
	}

	/**
	 * [isSuspended description]
	 * @return boolean [description]
	 */
	public function isSuspended()
	{
		return (bool) $this->suspended;
	}

	public function igniteModel()
	{
		$class = '\\'.ltrim($this->userMetadataModel, '\\');

		return new $class;
	}

}