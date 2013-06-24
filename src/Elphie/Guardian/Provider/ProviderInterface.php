<?php namespace Elphie\Guardian\Provider;

interface ProviderInterface {

	public function findById($id);

	public function findAll();

	public function create(array $attributes);

}