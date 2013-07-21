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

	'group_table' => 'groups',

	'user_group_table' => 'user_groups',

	'default' => array(
		'group' => 'superadmin',
		'user' => array(
			'email' => 'superadmin@example.com',
			'password' => '123456',
			'first_name' => 'Superadmin',
			'last_name' => 'Example'
		),
	),

);