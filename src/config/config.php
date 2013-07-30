<?php

/**
 * Laravel 4 user management package. Extending the Auth module.
 * 
 * @package  Elphie
 * @subpackage Guardian
 * @author  Ahmad Shah Hafizan Hamidin <[ahmadshahhafizan[at]gmail.com]>
 * @license  MIT
 */

return array(

	'user_table' => 'users',

	'metadata_table' => 'user_metadata',

	'group_table' => 'groups',

	'user_group_table' => 'user_groups',

	'default' => array(
		'group' => array(
			'name' => 'superadmin',
			'permissions' => array('panel' => 1, 'all' => 1)
		),
		'user' => array(
			'email' => 'superadmin@example.com',
			'password' => '123456',
			'first_name' => 'Superadmin',
			'last_name' => 'Example',
			'nickname' => 'superadmin'
		),
	),

);