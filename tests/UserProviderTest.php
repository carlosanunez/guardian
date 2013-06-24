<?php namespace Elphie\Guardian\Tests;

use Mockery as m;

class UserProviderTest extends \PHPUnit_Framework_TestCase {

	private $userProvider = null;

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

}