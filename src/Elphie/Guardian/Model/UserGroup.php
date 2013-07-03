<?php namespace Elphie\Guardian\Model;

use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model {

	protected $table = 'user_groups';

	public $timestamps = false;

	public function group()
	{
		return $this->belongsTo('Elphie\Guardian\Model\Group');
	}

}