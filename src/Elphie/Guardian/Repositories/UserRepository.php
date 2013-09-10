<?php namespace Elphie\Guardian\Repositories;

use Elphie\Guardian\UserNotFoundException;
use Elphie\Guardian\EmailAlreadyExistsException;
use Elphie\Guardian\EmailCannotBeEmptyException;
use Elphie\Guardian\NicknameAlreadyExistsException;
use Elphie\Guardian\Contracts\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface {

    protected $app;

    protected $model;

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function all(array $args = array())
    {
        if (empty($args)) return $this->model()->newQuery()->all();

        $model = $this->model()->newQuery();

        if (array_key_exists('where', $args))
        {
            $model = $this->buildQueryWhere($model, array_get($args, 'where'));
        }

        if (array_key_exists('orderBy', $args))
        {
            $model->orderBy(
                array_get($args, 'orderBy.attribute', 'created_at'),
                array_get($args, 'orderBy.direction', 'DESC')
            );
        }

        return (array_key_exists('paginate', $args)) ? $model->paginate(array_get($args, 'paginate', 20)) : $model->get();
    }

    public function findById($id)
    {
        $user = $this->model()->newQuery()->find($id);

        if ( ! $user) throw new UserNotFoundException("No user is found with the id [$id]");

        return $user;
    }

    public function findByLoginAttribute($login)
    {
        $model = $this->model();
        $user = $model->newQuery()->where($model->getLoginAttribute(), $login)->first();

        if ( ! $user) throw new UserNotFoundException("No user is found with {$model->getLoginAttribute()} [$login]");

        return $user;
    }

    public function findByAttribute($attribute, $value)
    {
        $user = $this->model()->newQuery()->where($attribute, $value)->first();

        if ( ! $user) throw new UserNotFoundException("No user is found with {$attribute} [$value]");

        return $user;
    }

    public function create(array $args = array())
    {
        $this->isValidForCreate(array_get($args, 'email', null), array_get($args, 'nickname', null));

        $model = $this->model();
        $user = $model->newQuery()->create($args);

        return $user;
    }

    public function update($id, array $args = array())
    {
        $this->isValidForUpdate($id, array_get($args, 'email', null));

        $user = $this->findById($id);
        $user->fill($args);
        $user->save();

        return $user;
    }

    public function delete($id)
    {

    }

    protected function model()
    {
        $this->model = $this->app['config']->get('elphie/guardian::models.user', 'Elphie\Guardian\Models\User');

        if (is_object($this->model))
        {
            return $this->model;
        }

        return new $this->model;
    }

    protected function buildQueryWhere($model, $args)
    {
        foreach($args as $key => $arg)
        {
            $operator = '=';
            $value = $arg;

            if(is_array($arg))
            {
                $operator = array_key_exists('operator', $arg) ? array_get($arg, 'operator') : '=';
                $value = array_key_exists('value', $arg) ? array_get($arg, 'value') : $arg;
            }

            $model = $model->where($key, $operator, $value);
        }

        return $model;
    }

    protected function isValidForCreate($email)
    {
        if (is_null($email)) throw new EmailCannotBeEmptyException('Email address cannot be empty');

        $model = $this->model();

        if ($model->newQuery()->where('email', $email)->first()) throw new EmailAlreadyExistsException("Email [$email] already exists");

        return true;
    }

    protected function isValidForUpdate($id, $email)
    {
        if (is_null($email)) throw new EmailCannotBeEmptyException('Email address cannot be empty');

        $user = $this->findById($id);

        if ($email != $user->email)
        {
            if ($this->model()->newQuery()->where('email', $email)->first()) throw new EmailAlreadyExistsException("Email [$email] already exists");
        }

        return true;
    }

}