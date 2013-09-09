<?php namespace Elphie\Guardian\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model {

    protected $table = 'groups';

    public $timestamps = false;

    protected $fillable = array('name', 'permissions');

}