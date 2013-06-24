<?php namespace Elphie\Guardian\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Support\Str;
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

	protected $loginAttribute = 'email';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'deleted_at', 'activation_code');

	protected $guarded = array('activation_code', 'reset_password_code');

	protected $fillable = array('email', 'password', 'first_name', 'last_name', 'activated');

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

	public function getLoginAttribute()
	{
		return $this->loginAttribute;
	}

	public function getActivationCode()
	{
		$this->activation_code = $activationCode = Str::random(40);

		$this->save();

		return $activationCode;
	}

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

	public function isActivated()
	{
		return (bool) $this->activated;
	}

}