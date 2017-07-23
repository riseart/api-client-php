<?php

namespace TDD\test\Auth\Adapter;

use PHPUnit\Framework\TestCase;
use Riseart\Api\Auth\Adapter\AbstractAdapter;
use Riseart\Api\Auth\Adapter\Visitor as AuthModuleVisitor;

class VisitorTest extends TestCase
{
    /**
     * @expectedException \TypeError
     */
    public function testConstructorWithoutParameters()
    {
        new AuthModuleVisitor();
    }


    /**
     *
     * @expectedException Riseart\Api\Exception\RiseartException
     * @expectedExceptionCode Riseart\Api\Exception\RiseartException::EXCEPTION_CODE_MISSED_REQUIRED_PARAMETER
     */
    public function testConstructorWithoutApiKey(){
        new AuthModuleVisitor([]);
    }

    /**
     *
     * @expectedException Riseart\Api\Exception\RiseartException
     * @expectedExceptionCode Riseart\Api\Exception\RiseartException::EXCEPTION_CODE_MISSED_REQUIRED_PARAMETER
     */
    public function testConstructorWithoutVisitorId(){
        new AuthModuleVisitor(['apiKey' => getenv('RISEART_APPLICATION_AUTH_API_KEY')]);
    }

    /**
     * @dataProvider getConfigDataProvider
     * @param $config
     */
    public function testConstructorWithValidConfig($config)
    {
        $instance = new AuthModuleVisitor($config);
        $payload = $instance->getPayload();
        $this->assertArrayHasKey('api_key', $payload);
        $this->assertArrayHasKey('auth_module', $payload);
        $this->assertArrayHasKey('visitor_id', $payload);
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
                    "visitorId" => getenv('RISEART_TESTS_DEFAULT_VISITOR_ID'),
                    "authGateway" => AbstractAdapter::AUTH_GATEWAY,
                    "apiKey" => getenv('RISEART_TESTS_VISITOR_AUTH_API_KEY')
                ]
            ]
        ];
    }
}