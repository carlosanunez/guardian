<?php namespace Elphie\Guardian\Model;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model {

	protected $table = 'user_groups';

	protected $fillable = array('user_id', 'group_id');

	public $timestamps = false;

	public function group()
	{
		return $this->belongsTo('Elphie\Guardian\Model\Group');
	}

}