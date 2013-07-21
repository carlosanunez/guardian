<?php namespace Elphie\Guardian\Provider;

/**
 * Laravel 4 user management package. Extending the Auth module.
 * 
 * @package  Elphie
 * @subpackage Guardian
 * @author  Ahmad Shah Hafizan Hamidin <[ahmadshahhafizan[at]gmail.com]>
 * @license  MIT
 */

interface ProviderInterface {

	/**
	 * Find result by primary key
	 * 
	 * @param  integer $id
	 * @return void
	 */
	public function findById($id);

	/**
	 * Return all result
	 * 
	 * @return void
	 */
	public function findAll();

	/**
	 * Create new item
	 * 
	 * @param  array  $attributes
	 * @return void
	 */
	public function create(array $attributes);

}