<?php namespace Elphie\Guardian\Tests\Repositories;

use Mockery as m;
use Elphie\Guardian\Models\User;
use Elphie\Guardian\Repositories\UserRepository;

class UserRepositoryTest extends \PHPUnit_Framework_TestCase {

    protected $app;

    protected $model;

    protected $query;

    public function setUp()
    {
        $this->app = array(
            'config' => m::mock('Config')
        );

        $this->query = m::mock('stdClass');
        $this->model = m::mock('Elphie\Guardian\Models\User');
    }

    public function tearDown()
    {
        unset($this->app);
        unset($this->model);
        unset($this->query);

        m::close();
    }

    public function testAllMethod()
    {
        $repo = $this->getInstance();

        $this->query->shouldReceive('newQuery')->once()->andReturn($this->query);
        $this->query->shouldReceive('all')->andReturn(array($model = $this->model));

        $this->assertEquals(array($model), $repo->all());
    }

    public function testAllMethodWithWhere()
    {
        $args = array(
            'where' => array(
                'activated' => 1,
                'suspended' => array('operator' => '!=', 'value' => '1')
            )
        );

        $repo = $this->getInstance();

        $this->query->shouldReceive('newQuery')->once()->andReturn($this->query);
        $this->query->shouldReceive('where')->with('activated', '=', 1)->once()->andReturn($this->query);
        $this->query->shouldReceive('where')->with('suspended', '!=', 1)->once()->andReturn($this->query);
        $this->query->shouldReceive('get')->andReturn(array($model = $this->model));

        $this->assertEquals(array($model), $repo->all($args));
    }

    public function testAllMethodWithOrderBy()
    {
        $args = array(
            'orderBy' => array(
                'attribute' => 'updated_at',
                'direction' => 'DESC'
            )
        );

        $repo = $this->getInstance();

        $this->query->shouldReceive('newQuery')->once()->andReturn($this->query);
        $this->query->shouldReceive('orderBy')->with($args['orderBy']['attribute'], $args['orderBy']['direction'])->andReturn($this->query);
        $this->query->shouldReceive('get')->andReturn(array($model = $this->model));

        $this->assertEquals(array($model), $repo->all($args));
    }

    public function testAllMethodWithPaginate()
    {
        $args = array(
            'paginate' => 30
        );

        $repo = $this->getInstance();

        $this->query->shouldReceive('newQuery')->once()->andReturn($this->query);
        $this->query->shouldReceive('paginate')->with($args['paginate'])->once()->andReturn($model = m::mock('Illuminate\Pagination\Paginator'));

        $this->assertEquals($model, $repo->all($args));
    }

    public function testFindByIdMethod()
    {
        $repo = $this->getInstance();

        $this->query->shouldReceive('newQuery')->once()->andReturn($this->query);
        $this->query->shouldReceive('find')->with(1)->andReturn($model = $this->model);

        $this->assertEquals($model, $repo->findById(1));
    }

    /**
     * @expectedException Elphie\Guardian\UserNotFoundException
     */
    public function testFindByIdMethodThrowUserNotFoundException()
    {
        $repo = $this->getInstance();

        $this->query->shouldReceive('newQuery')->once()->andReturn($this->query);
        $this->query->shouldReceive('find')->with(1)->andReturn(null);

        $repo->findById(1);
    }

    public function testFindByLoginAttributeMethod()
    {
        $attribute = 'email';
        $email = 'foo@bar.com';

        $repo = $this->getInstance();

        $this->query->shouldReceive('getLoginAttribute')->once()->andReturn($attribute);
        $this->query->shouldReceive('newQuery')->once()->andReturn($this->query);
        $this->query->shouldReceive('where')->with($attribute, $email)->once()->andReturn($this->query);
        $this->query->shouldReceive('first')->andReturn($model = $this->model);

        $this->assertEquals($model, $repo->findByLoginAttribute($email));
    }

    /**
     * @expectedException Elphie\Guardian\UserNotFoundException
     */
    public function testFindByLoginAttributeMethodThrowUserNotFoundException()
    {
        $attribute = 'email';
        $email = 'foo@bar.com';

        $repo = $this->getInstance();

        $this->query->shouldReceive('getLoginAttribute')->twice()->andReturn($attribute);
        $this->query->shouldReceive('newQuery')->once()->andReturn($this->query);
        $this->query->shouldReceive('where')->with($attribute, $email)->once()->andReturn($this->query);
        $this->query->shouldReceive('first')->andReturn(null);

        $repo->findByLoginAttribute($email);
    }

    public function testfindByAttributeMethod()
    {
        $attribute = 'nickname';
        $value = 'foobar';

        $repo = $this->getInstance();

        $this->query->shouldReceive('newQuery')->once()->andReturn($this->query);
        $this->query->shouldReceive('where')->with($attribute, $value)->once()->andReturn($this->query);
        $this->query->shouldReceive('first')->andReturn($model = $this->model);

        $this->assertEquals($model, $repo->findByAttribute($attribute, $value));
    }

    /**
     * @expectedException Elphie\Guardian\UserNotFoundException
     */
    public function testfindByAttributeMethodThrowUserNotFoundException()
    {
        $attribute = 'nickname';
        $value = 'foobar';

        $repo = $this->getInstance();

        $this->query->shouldReceive('newQuery')->once()->andReturn($this->query);
        $this->query->shouldReceive('where')->with($attribute, $value)->once()->andReturn($this->query);
        $this->query->shouldReceive('first')->andReturn(null);

        $repo->findByAttribute($attribute, $value);
    }

    public function testCreateMethod()
    {
        $args = array('email' => 'foo@bar.com', 'password' => '123456', 'nickname' => 'foobar', 'first_name' => 'foo', 'last_name' => 'bar');
        $repo = $this->getInstance();

        $this->query->shouldReceive('newQuery')->twice()->andReturn($this->query);
        $this->query->shouldReceive('where')->with('email', $args['email'])->andReturn($this->query);
        $this->query->shouldReceive('first')->andReturn(null);
        $this->query->shouldReceive('create')->with($args)->andReturn($model = $this->model);

        $this->assertEquals($model, $repo->create($args));
    }

    /**
     * @expectedException Elphie\Guardian\EmailCannotBeEmptyException
     */
    public function testCreateMethodThrowEmailCannotBeEmptyException()
    {
        $args = array('');
        $repo = $this->getInstance();

        $repo->create($args);
    }

    /**
     * @expectedException Elphie\Guardian\EmailAlreadyExistsException
     */
    public function testCreateMethodThrowEmailAlreadyExistsException()
    {
        $args = array('email' => 'foo@bar.com');
        $repo = $this->getInstance();

        $this->query->shouldReceive('newQuery')->once()->andReturn($this->query);
        $this->query->shouldReceive('where')->with('email', $args['email'])->once()->andReturn($this->query);
        $this->query->shouldReceive('first')->andReturn($model = $this->model);

        $repo->create($args);
    }

    public function testUpdateMethod()
    {
        $args = array('email' => 'foo@bar.com');
        $repo = $this->getInstance();

        $this->query->shouldReceive('newQuery')->zeroOrMoreTimes()->andReturn($this->query);
        $this->query->shouldReceive('find')->with(1)->zeroOrMoreTimes()->andReturn($user = $this->getUserStub());

        $this->assertNotEquals($args['email'], $user->email);

        $this->query->shouldReceive('where')->with('email', $args['email'])->once()->andReturn($this->query);
        $this->query->shouldReceive('first')->once()->andReturn(null);

        $user->shouldReceive('fill')->with($args)->once()->andReturn($user);
        $user->shouldReceive('save')->once()->andReturn($user);

        $this->assertEquals($user, $repo->update(1, $args));
    }

    /**
     * @expectedException Elphie\Guardian\EmailCannotBeEmptyException
     */
    public function testUpdateMethodThrowEmailCannotBeEmptyException()
    {
        $args = array();
        $repo = $this->getInstance();

        $repo->update(1, $args);
    }

    /**
     * @expectedException Elphie\Guardian\EmailAlreadyExistsException
     */
    public function testUpdateMethodThrowEmailAlreadyExistsException()
    {
        $args = array('email' => 'foo@bar.com');
        $repo = $this->getInstance();

        $this->query->shouldReceive('newQuery')->twice()->andReturn($this->query);
        $this->query->shouldReceive('find')->with(1)->once()->andReturn($user = $this->getUserStub());

        $this->assertNotEquals($args['email'], $user->email);

        $this->query->shouldReceive('where')->with('email', $args['email'])->once()->andReturn($this->query);
        $this->query->shouldReceive('first')->once()->andReturn($model = $this->model);

        $repo->update(1, $args);
    }

    protected function getInstance()
    {
        $this->app['config']->shouldReceive('get')->with('elphie/guardian::models.user', 'Elphie\Guardian\Models\User')->andReturn($this->query);
        return new UserRepository($this->app);
    }

    protected function getUserStub()
    {
        $user = m::mock('stdClass');
        $user->id = 1;
        $user->email = 'foobar@bar.com';
        $user->nickname = 'foobar';
        $user->password = '123456';
        $user->first_name = 'Foo';
        $user->last_name = 'Bar';

        return $user;
    }

}
