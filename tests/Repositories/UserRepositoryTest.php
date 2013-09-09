<?php namespace Elphie\Guardian\Tests\Repositories;

use Mockery as m;
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

    public function findByAttributeMethod()
    {
        $attribute = 'nickname';
        $value = 'foobar';

        $this->query->shouldReceive('newQuery')->once()->andReturn($this->query);
        $this->query->shouldReceive('where')->with($attribute, $value)->once()->andReturn($this->query);
        $this->query->shouldReceive('first')->andReturn($model = $this->model);

        $this->assertEquals($model, $repo->findByAttribute($attribute, $value));
    }

    /**
     * @expectedException Elphie\Guardian\UserNotFoundException
     */
    public function findByAttributeMethodThrowUserNotFoundException()
    {
        $attribute = 'nickname';
        $value = 'foobar';

        $this->query->shouldReceive('newQuery')->once()->andReturn($this->query);
        $this->query->shouldReceive('where')->with($attribute, $value)->once()->andReturn($this->query);
        $this->query->shouldReceive('first')->andReturn(null);

        $repo->findByAttribute($attribute, $value);
    }

    protected function getInstance()
    {
        $this->app['config']->shouldReceive('get')->with('elphie/guardian::models.user', 'Elphie\Guardian\Models\User')->andReturn($this->query);
        return new UserRepository($this->app);
    }

}
