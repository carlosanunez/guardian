<?php namespace Elphie\Guardian\Model;

/**
 * Larvel 4 user management package. Extending the Auth module.
 * 
 * @package  Elphie
 * @subpackage Guardian
 * @author  Ahmad Shah Hafizan Hamidin <[ahmadshahhafizan[at]gmail.com]>
 * @license  MIT
 */

use Illuminate\Database\Eloquent\Model;

class UserMetadata extends Model {

	protected $table = 'user_metadatas';

	public $timestamps = false;

	protected $hidden = array('id', 'user_id');

	protected $fillable = array('user_id', 'key', 'value');

	public function user()
	{
		return $this->belongsTo('Elphie\Guardian\Model\User', 'user_id');
	}

}