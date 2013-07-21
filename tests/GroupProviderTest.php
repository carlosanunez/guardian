<?php namespace Elphie\Guardian\Tests;

/**
 * Laravel 4 user management package. Extending the Auth module.
 * 
 * @package  Elphie
 * @subpackage Guardian
 * @author  Ahmad Shah Hafizan Hamidin <[ahmadshahhafizan[at]gmail.com]>
 * @license  MIT
 */

use Mockery as m;

class GroupProviderTest extends \PHPUnit_Framework_TestCase {

	private $groupProvider = null;

	private $groupModel = null;

	private $query = null;

	private $groupName = 'superadmin';

	public function setUp()
	{
		$this->groupProvider = m::mock('Elphie\Guardian\Provider\GroupProvider[igniteModel]');
		$this->groupModel = m::mock('Elphie\Guardian\Model\Group');
		$this->query = m::mock('StdClass');
	}

	public function tearDown()
	{
		m::close();

		$this->groupProvider = null;
		$this->groupModel = null;
		$this->query = null;
	}

	public function testFindById()
	{
		$this->query->shouldReceive('newQuery')->andReturn($this->query);
		$this->query->shouldReceive('find')->with(1)->andReturn($group = $this->groupModel);

		$this->groupProvider->shouldReceive('igniteModel')->once()->andReturn($this->query);
		$this->assertEquals($group, $this->groupProvider->findById(1));
	}

	/**
	 * @expectedException Elphie\Guardian\GroupNotFoundException
	 */
	public function testFindByIdThrowsException()
	{
		$this->query->shouldReceive('newQuery')->andReturn($this->query);
		$this->query->shouldReceive('find')->with(1)->andReturn(null);

		$this->groupProvider->shouldReceive('igniteModel')->once()->andReturn($this->query);
		$this->groupProvider->findById(1);
	}

	public function testFindByName()
	{
		$this->query->shouldReceive('newQuery')->andReturn($this->query);
		$this->query->shouldReceive('where')->with('name', '=', $this->groupName)->once()->andReturn($this->query);
		$this->query->shouldReceive('first')->andReturn($group= $this->groupModel);

		$this->groupProvider->shouldReceive('igniteModel')->once()->andReturn($this->query);
		$this->assertEquals($group, $this->groupProvider->findByName($this->groupName));
	}

	/**
	 * @expectedException Elphie\Guardian\GroupNotFoundException
	 */
	public function testFindByNameThrowsException()
	{
		$this->query->shouldReceive('newQuery')->andReturn($this->query);
		$this->query->shouldReceive('where')->with('name', '=', $this->groupName)->once()->andReturn($this->query);
		$this->query->shouldReceive('first')->andReturn(null);

		$this->groupProvider->shouldReceive('igniteModel')->andReturn($this->query);
		$this->groupProvider->findByName($this->groupName);
	}

	public function testFindAll()
	{
		$this->groupProvider->shouldReceive('igniteModel')->once()->andReturn($group = $this->groupModel);
		$group->shouldReceive('newQuery')->once()->andReturn($this->query);
		$this->query->shouldReceive('get')->once()->andReturn($collection = m::mock('StdClass'));
		$collection->shouldReceive('all')->once()->andReturn(array($group = $this->groupModel));

		$this->assertEquals(array($group), $this->groupProvider->findAll());
	}

}