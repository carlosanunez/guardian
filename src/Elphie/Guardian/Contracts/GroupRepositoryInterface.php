<?php namespace Elphie\Guardian\Contracts;

interface GroupRepositoryInterface {

    public function all(array $args = array());

    public function findById($id);

    public function findByName($name);

    public function create(array $args);

    public function update($id, array $args);

    public function delete($id);

}