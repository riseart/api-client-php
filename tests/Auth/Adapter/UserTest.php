<?php

namespace TDD\test\Auth\Adapter;

use PHPUnit\Framework\TestCase;
use Riseart\Api\Auth\Adapter\AbstractAdapter;
use Riseart\Api\Auth\Adapter\User as AuthModuleUser;

class UserTest extends TestCase
{
    /**
     * @expectedException \TypeError
     */
    public function testConstructorWithoutParameters()
    {
        new AuthModuleUser();
    }


    /**
     *
     * @expectedException Riseart\Api\Exception\RiseartException
     * @expectedExceptionCode Riseart\Api\Exception\RiseartException::EXCEPTION_CODE_MISSED_REQUIRED_PARAMETER
     */
    public function testConstructorWithoutApiKey(){
        new AuthModuleUser([]);
    }

    /**
     *
     * @expectedException Riseart\Api\Exception\RiseartException
     * @expectedExceptionCode Riseart\Api\Exception\RiseartException::EXCEPTION_CODE_MISSED_REQUIRED_PARAMETER
     */
    public function testConstructorWithoutUsername(){
        new AuthModuleUser([
            'apiKey' => getenv('RISEART_APPLICATION_AUTH_API_KEY')
        ]);
    }

    /**
     *
     * @expectedException Riseart\Api\Exception\RiseartException
     * @expectedExceptionCode Riseart\Api\Exception\RiseartException::EXCEPTION_CODE_MISSED_REQUIRED_PARAMETER
     */
    public function testConstructorWithoutPassword(){
        new AuthModuleUser([
            'apiKey' => getenv('RISEART_APPLICATION_AUTH_API_KEY'),
            'username' => getenv('RISEART_APPLICATION_AUTH_API_KEY')
        ]);
    }

    /**
     * @dataProvider getConfigDataProvider
     * @param $config
     */
    public function testConstructorWithValidConfig($config)
    {
        $instance = new AuthModuleUser($config);
        $payload = $instance->getPayload();
        $this->assertArrayHasKey('api_key', $payload);
        $this->assertArrayHasKey('auth_module', $payload);
        $this->assertArrayHasKey('username', $payload);
        $this->assertArrayHasKey('password', $payload);
    }

    /**
     *
     */
    public function getConfigDataProvider()
    {
        return [
            [
                [
                    "verifySSL" => false,
                    "authGateway" => AbstractAdapter::AUTH_GATEWAY,
                    "apiKey" => getenv('RISEART_TESTS_VISITOR_AUTH_API_KEY'),
                    "username" => getenv('RISEART_TESTS_DEFAULT_USERNAME'),
                    "password" => getenv('RISEART_TESTS_DEFAULT_PASSWORD'),
                ]
            ]
        ];
    }
}