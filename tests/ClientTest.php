<?php

namespace TDD\test;

use PHPUnit\Framework\TestCase;
use Riseart\Api\Auth\Adapter\Application as AuthModuleApplication;
use Riseart\Api\Auth\Adapter\InterfaceAdapter;
use Riseart\Api\Client;
use Riseart\Api\Token\RiseartToken;
use Riseart\Api\Client as RiseartClient;

class ClientTest extends TestCase
{
    /**
     * Client
     * @var Client $Client
     */
    protected $Client;

    /**
     * Stub valid token
     * @var RiseartToken $ValidToken
     */
    protected $ValidToken;

    /**
     * @var Application $ValidAppAuthModule
     */
    protected $ValidAppAuthModule;

    /**
     * Configures
     */
    public function setUp()
    {
        $this->Client = new RiseartClient();
        $this->ValidToken = $this->getValidToken();
        $this->ValidAppAuthModule = $this->getValidAppAuthModule();
    }

    /**
     * CLeans
     */
    public function tearDown()
    {
        unset($this->Client);
    }

    /**
     * RiseartToken stub
     * @return \PHPUnit_Framework_MockObject_MockObject|RiseartToken
     */
    protected function getValidToken()
    {
        $stub = $this->getMockBuilder(RiseartToken::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $stub->method('validate')->willReturn(true);
        return $stub;
    }

    /**
     * Application stub
     * @return \PHPUnit_Framework_MockObject_MockObject|Application
     */
    protected function getValidAppAuthModule()
    {
        $stub = $this->getMockBuilder(AuthModuleApplication::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $stub->method('getPayload')->willReturn([
            'api_key' => getenv('RISEART_TESTS_APPLICATION_AUTH_API_KEY'),
            'auth_module' => AuthModuleApplication::AUTH_MODULE_NAME,
        ]);
        return $stub;
    }

    /**
     * @return array
     */
    public function invalidVersionDataProvider()
    {
        return [
            [''],
            ['1']
        ];
    }

    /**
     * @return array
     */
    public function validVersionDataProvider()
    {
        return [
            ['v1'],
            ['v2']
        ];
    }

    /**
     * No config
     */
    public function testConstructorWithoutParameters()
    {
        $this->assertInstanceOf(RiseartClient::class, $this->Client);
    }

    /**
     * Invalid Host
     * @expectedException Riseart\Api\Exception\RiseartException
     * @expectedExceptionCode Riseart\Api\Exception\RiseartException::EXCEPTION_CODE_INVALID_HOST
     */
    public function testConstructorInvalidHostScheme()
    {
        $dummyConfig = ['host' => 'http://api.com'];
        new RiseartClient($dummyConfig);
    }

    /**
     * Invalid Host
     * @expectedException Riseart\Api\Exception\RiseartException
     * @expectedExceptionCode Riseart\Api\Exception\RiseartException::EXCEPTION_CODE_INVALID_HOST
     */
    public function testConstructorInvalidHostDomain()
    {
        $dummyConfig = ['host' => 'https://api.com'];
        new RiseartClient($dummyConfig);
    }

    /**
     * Valid Host
     */
    public function testConstructorValidHost()
    {
        $dummyConfig = ['host' => RiseartClient::API_HOST];
        $this->assertInstanceOf(RiseartClient::class, new RiseartClient($dummyConfig));
    }

    /**
     * Invalid version
     * @dataProvider invalidVersionDataProvider
     * @expectedException Riseart\Api\Exception\RiseartException
     * @expectedExceptionCode Riseart\Api\Exception\RiseartException::EXCEPTION_CODE_INVALID_API_VERSION
     * @param string $version
     */
    public function testConstructorInvalidVersion($version)
    {
        $dummyConfig = ['defaultVersion' => $version];
        $this->assertInstanceOf(RiseartClient::class, new RiseartClient($dummyConfig));
    }

    /**
     * Valid Version
     * @dataProvider validVersionDataProvider
     * @param $version
     */
    public function testConstructorValidVersion($version)
    {
        $dummyConfig = ['defaultVersion' => $version];
        $this->assertInstanceOf(RiseartClient::class, new RiseartClient($dummyConfig));
    }

    /**
     * Valid Auth Adapter
     */
    public function testConstructorValidAuthAdapter()
    {
        $dummyConfig = ['authAdapter' => $this->ValidAppAuthModule];
        $instance = new RiseartClient($dummyConfig);

        $this->assertTrue($instance->getAuthAdapter() instanceof InterfaceAdapter);
    }

    /**
     * Valid Token
     */
    public function testConstructorValidToken()
    {
        $instance = new RiseartClient(['token' => $this->ValidToken]);
        $this->assertInstanceOf(RiseartToken::class, $instance->getToken());
    }

}
