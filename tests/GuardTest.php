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

    public function testRegisterMethod()
    {
        $args = array('email' => 'foo@bar.com', 'password' => '123456', 'nickname' => 'foobar', 'first_name' => 'foo', 'last_name' => 'bar');
        $activationCode = 'IwvMYbniZL67z7gVyhib';
        $mock = $this->getGuard();

        $mock->getUserRepository()->shouldReceive('create')->once()->andReturn($user = $this->getUserStub());
        $user->shouldReceive('generateRandomCode')->once()->andReturn($activationCode);
        $mock->getUserRepository()->shouldReceive('update')->with($user->id, array('activation_code' => $activationCode))->andReturn($user);
        $user->shouldReceive('getActivationCode')->once()->andReturn($user->activation_code = $activationCode);

        $this->assertEquals($user->activation_code, $mock->register($args));
    }

    public function testRegisterMethodForceActivation()
    {
        $args = array('email' => 'foo@bar.com', 'password' => '123456', 'nickname' => 'foobar', 'first_name' => 'foo', 'last_name' => 'bar');
        $activationCode = 'IwvMYbniZL67z7gVyhib';
        $mock = $this->getGuard();

        $mock->getUserRepository()->shouldReceive('create')->once()->andReturn($user = $this->getUserStub());
        $user->shouldReceive('generateRandomCode')->once()->andReturn($activationCode);
        $mock->getUserRepository()->shouldReceive('update')->with($user->id, array('activation_code' => $activationCode, 'activated' => 1))->andReturn($user);
        $user->shouldReceive('getActivationCode')->once()->andReturn($user->activation_code = $activationCode);

        $this->assertEquals($user->activation_code, $mock->register($args, true));
    }

    public function testActivateAccountMethod()
    {
        $activationCode = 'IwvMYbniZL67z7gVyhib';
        $user = m::mock('Illuminate\Auth\UserInterface');
        $user->shouldReceive('isActivated')->once()->andReturn(false);
        $user->shouldReceive('getActivationCode')->once()->andReturn($activationCode);
        $user->shouldReceive('save')->once()->andReturn($user);

        $mock = $this->getGuard();
        $mock->setUser($user);
        $this->assertTrue($mock->activateAccount($activationCode));
    }

    public function testActivateAccountMethodFiresActivateAccountEvent()
    {
        $activationCode = 'IwvMYbniZL67z7gVyhib';
        $user = m::mock('Illuminate\Auth\UserInterface');
        $user->shouldReceive('isActivated')->once()->andReturn(false);
        $user->shouldReceive('getActivationCode')->once()->andReturn($activationCode);
        $user->shouldReceive('save')->once()->andReturn($user);

        $mock = $this->getGuard();
        $mock->setUser($user);
        $mock->setDispatcher($events = m::mock('Illuminate\Events\Dispatcher'));
        $events->shouldReceive('fire')->once()->with('elphie.guardian.activateaccount', array($user));
        $mock->activateAccount($activationCode);
    }

    protected function getGuard()
    {
        list($session, $provider, $config, $user, $request, $cookie) = $this->getMocks();
        return new Guard($provider, $session, $config, $user, $request);
    }

    protected function getMocks()
    {
        return array(
            m::mock('Illuminate\Session\Store'),
            m::mock('Illuminate\Auth\UserProviderInterface'),
            m::mock('Illuminate\Config\Repository'),
            m::mock('Elphie\Guardian\Contracts\UserRepositoryInterface'),
            Request::create('/', 'GET'),
            m::mock('Illuminate\Cookie\CookieJar'),
        );
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