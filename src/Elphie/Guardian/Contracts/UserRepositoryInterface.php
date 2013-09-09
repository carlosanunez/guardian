<?php namespace Elphie\Guardian\Contracts;

interface UserRepositoryInterface {

    public function all(array $args = array());

    public function findById($id);

    public function findByLoginAttribute($login);

    public function findByAttribute($attribute, $value);

    public function create(array $args = array());

    public function update($id, array $args = array());

    public function delete($id);

}