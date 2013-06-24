<?php namespace Elphie\Guardian\Tests;

use Mockery as m;

class UserProviderTest extends \PHPUnit_Framework_TestCase {

	/**
	 * Elphie\Guardian\Provider\UserProvider
	 *
	 * @var string
	 */
	private $userProvider = null;

	/**
	 * Elphie\Guardian\Model\User
	 *
	 * @var string
	 */
	private $userModel = null;

	public function setUp()
	{
		$this->userProvider = m::mock('Elphie\Guardian\Provider\UserProvider[igniteModel]');
		$this->userModel = m::mock('Elphie\Guardian\Model\User');
	}

	public function tearDown()
	{
		m::close();
	}

	public function testFindById()
	{
		$query = m::mock('StdClass');
		$query->shouldReceive('newQuery')->andReturn($query);
		$query->shouldReceive('find')->with(1)->andReturn($user = $this->userModel);

		$this->userProvider->shouldReceive('igniteModel')->once()->andReturn($query);
		$this->assertEquals($user, $this->userProvider->findById(1));
	}

	/**
	 * @expectedException Elphie\Guardian\UserNotFoundException
	 */
	public function testFindByIdThrowsException()
	{
		$query = m::mock('StdClass');
		$query->shouldReceive('newQuery')->andReturn($query);
		$query->shouldReceive('find')->with(1)->andReturn(null);

		$this->userProvider->shouldReceive('igniteModel')->once()->andReturn($query);
		$this->userProvider->findById(1);
	}

	public function testFindByLogin()
	{
		$loginAttribute = 'email';
		$email = 'foo@bar.com';

		$query = m::mock('StdClass');
		$query->shouldReceive('getLoginAttribute')->andReturn($loginAttribute);
		$query->shouldReceive('newQuery')->andReturn($query);
		$query->shouldReceive('where')->with($loginAttribute, '=', $email)->once()->andReturn($query);
		$query->shouldReceive('first')->andReturn($user = $this->userModel);

		$this->userProvider->shouldReceive('igniteModel')->once()->andReturn($query);
		$this->assertEquals($user, $this->userProvider->findByLogin($email));
	}

	/**
	 * @expectedException Elphie\Guardian\UserNotFoundException
	 */
	public function testFindByLoginThrowsException()
	{
		$loginAttribute = 'email';
		$email = 'foo@bar.com';

		$query = m::mock('StdClass');
		$query->shouldReceive('getLoginAttribute')->andReturn($loginAttribute);
		$query->shouldReceive('newQuery')->andReturn($query);
		$query->shouldReceive('where')->with($loginAttribute, '=', $email)->once()->andReturn($query);
		$query->shouldReceive('first')->andReturn(null);

		$this->userProvider->shouldReceive('igniteModel')->once()->andReturn($query);
		$this->userProvider->findByLogin($email);
	}

	public function testFindByActivationCode()
	{
		$activationCode = 'k2vvPG29hYF89Zs0utya8f3Vj8jU1hjxSFCHK21m';

		$query = m::mock('StdClass');
		$query->shouldReceive('newQuery')->andReturn($query);
		$query->shouldReceive('where')->with('activation_code', '=', $activationCode)->once()->andReturn($query);
		$query->shouldReceive('first')->andReturn($user = $this->userModel);

		$this->userProvider->shouldReceive('igniteModel')->once()->andReturn($query);
		$this->assertEquals($user, $this->userProvider->findByActivationCode($activationCode));
	}

	/**
	 * @expectedException Elphie\Guardian\UserNotFoundException
	 */
	public function testFindByActivationCodeThrowsException()
	{
		$activationCode = 'k2vvPG29hYF89Zs0utya8f3Vj8jU1hjxSFCHK21m';

		$query = m::mock('StdClass');
		$query->shouldReceive('newQuery')->andReturn($query);
		$query->shouldReceive('where')->with('activation_code', '=', $activationCode)->once()->andReturn($query);
		$query->shouldReceive('first')->andReturn(null);

		$this->userProvider->shouldReceive('igniteModel')->once()->andReturn($query);
		$this->userProvider->findByActivationCode($activationCode);
	}

	public function testFindAll()
	{
		$this->userProvider->shouldReceive('igniteModel')->once()->andReturn($user = $this->userModel);
		$query = m::mock('StdClass');
		$user->shouldReceive('newQuery')->once()->andReturn($query);
		$query->shouldReceive('get')->once()->andReturn($collection = m::mock('StdClass'));
		$collection->shouldReceive('all')->once()->andReturn(array($user = $this->userModel));

		$this->assertEquals(array($user), $this->userProvider->findAll());
	}

	public function testCreateUser()
	{
		$attributes = array(
			'email' => 'foo@bar.com',
			'password' => 'foobar',
			'first_name' => 'foo',
			'last_name' => 'bar'
		);

		$this->userProvider->shouldReceive('igniteModel')->once()->andReturn($user = $this->userModel);
		$user->shouldReceive('fill')->with($attributes)->once();
		$user->shouldReceive('save')->once();

		$this->assertEquals($user, $this->userProvider->create($attributes));
	}

}