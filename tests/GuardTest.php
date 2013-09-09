<?php namespace Elphie\Guardian\Tests;

use Mockery as m;
use Elphie\Guardian\Guard;
use Symfony\Component\HttpFoundation\Request;

class GuardTest extends \PHPUnit_Framework_TestCase {

    public function tearDown()
    {
        m::close();
    }

    /**
     * @expectedException Elphie\Guardian\UserNotLoginException
     */
    public function testLogoutMethodThrowUserNotLoginException()
    {
        $mock = $this->getGuard();
        $mock->setCookieJar($cookies = m::mock('Illuminate\Cookie\CookieJar'));
        $cookies->shouldReceive('get')->once()->andReturn(null);
        $mock->getSession()->shouldReceive('get')->once()->andReturn(null);
        $mock->logout();
    }

    protected function getGuard()
    {
        list($session, $provider, $user, $request, $cookie) = $this->getMocks();
        return new Guard($provider, $session, $user, $request);
    }

    protected function getMocks()
    {
        return array(
            m::mock('Illuminate\Session\Store'),
            m::mock('Illuminate\Auth\UserProviderInterface'),
            m::mock('Elphie\Guardian\Contracts\UserRepositoryInterface'),
            Request::create('/', 'GET'),
            m::mock('Illuminate\Cookie\CookieJar'),
        );
    }

}