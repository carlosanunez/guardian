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

class UserGroup extends Model {

	protected $table = 'user_groups';

	protected $fillable = array('user_id', 'group_id');

	public $timestamps = false;

	public function group()
	{
		return $this->belongsTo('Elphie\Guardian\Model\Group');
	}

	public function user()
	{
		return $this->belongsTo('Elphie\Guardian\Model\User');
	}

}