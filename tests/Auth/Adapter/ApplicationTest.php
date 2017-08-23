<?php

namespace TDD\test\Auth\Adapter;

use PHPUnit\Framework\TestCase;
use Riseart\Api\Auth\Adapter\AbstractAdapter;
use Riseart\Api\Auth\Adapter\Application as AuthModuleApplication;
use SebastianBergmann\GlobalState\RuntimeException;

class ApplicationTest extends TestCase
{
    /**
     * @expectedException \TypeError
     */
    public function testConstructorWithoutParameters()
    {
        new AuthModuleApplication();
    }

    /**
     *
     * @expectedException Riseart\Api\Exception\RiseartException
     * @expectedExceptionCode Riseart\Api\Exception\RiseartException::EXCEPTION_CODE_MISSED_REQUIRED_PARAMETER
     */
    public function testConstructorWithoutApiKey()
    {
        new AuthModuleApplication([]);
    }

    /**
     * @dataProvider getConfigDataProvider
     * @param $config
     */
    public function testConstructorWithValidConfig($config)
    {
        $instance = new AuthModuleApplication($config);
        $payload = $instance->getPayload();
        $this->assertArrayHasKey('api_key', $payload);
        $this->assertArrayHasKey('auth_module', $payload);

    }

    /**
     * @return array
     */
    public function getConfigDataProvider()
    {
        return [[[
            "apiKey" => getenv('RISEART_TESTS_APPLICATION_AUTH_API_KEY')
        ]]];
    }
}