<?php

namespace TDD\test\Auth\Adapter;

use PHPUnit\Framework\TestCase;
use Riseart\Api\Auth\Adapter\AbstractAdapter;
use Riseart\Api\Auth\Adapter\ApplicationUser as AuthApplicationUser;

class ApplicationUserTest extends TestCase
{
    /**
     *
     * @expectedException \TypeError
     */
    public function testConstructorWithoutParameters()
    {
        new AuthApplicationUser();
    }

    /**
     *
     * @expectedException Riseart\Api\Exception\RiseartException
     * @expectedExceptionCode Riseart\Api\Exception\RiseartException::EXCEPTION_CODE_MISSED_REQUIRED_PARAMETER
     */
    public function testConstructorWithoutApiKey()
    {
        new AuthApplicationUser([]);
    }

    /**
     *
     * @expectedException Riseart\Api\Exception\RiseartException
     * @expectedExceptionCode Riseart\Api\Exception\RiseartException::EXCEPTION_CODE_MISSED_REQUIRED_PARAMETER
     */
    public function testConstructorWithoutUserId()
    {
        $module = new AuthApplicationUser(
            $this->getConfigDataProvider(
                true
            )
        );
    }

    /**
     *
     */
    public function testConstructorWithUserId()
    {
        /** @var AuthApplicationUser $module */
        $module = new AuthApplicationUser(
            $this->getConfigDataProvider(
                true,
                true
            )
        );
        $this->assertInternalType("int", $module->getUserId());
    }

    /**
     *
     */
    public function testConstructorWithAclRole()
    {
        /** @var AuthApplicationUser $module */
        $module = new AuthApplicationUser(
            $this->getConfigDataProvider(
                true,
                true,
                true
            )
        );
        $this->assertInternalType("int", $module->getUserId());
        $this->assertInternalType("string", $module->getAclRole());
    }

    /**
     *
     */
    public function testPayloadIsArray()
    {
        /** @var AuthApplicationUser $module */
        $module = new AuthApplicationUser(
            $this->getConfigDataProvider(
                true,
                true,
                true
            )
        );
        $this->assertInternalType("array", $module->getPayload());
    }

    /**
     *
     */
    public function testPayloadIsValidWithAclRole()
    {
        $module = new AuthApplicationUser(
            $this->getConfigDataProvider(
                true,
                true,
                true
            )
        );
        $payload = $module->getPayload();
        $this->assertArrayHasKey('api_key', $payload);
        $this->assertArrayHasKey('auth_module', $payload);
        $this->assertArrayHasKey('user_id', $payload);
        $this->assertArrayHasKey('acl_role', $payload);
    }

    /**
     *
     */
    public function testPayloadIsValidWithoutAclRole()
    {
        $module = new AuthApplicationUser(
            $this->getConfigDataProvider(
                true,
                true
            )
        );
        $payload = $module->getPayload();
        $this->assertArrayHasKey('api_key', $payload);
        $this->assertArrayHasKey('auth_module', $payload);
        $this->assertArrayHasKey('user_id', $payload);
    }


    /**
     * @param bool $apiKey
     * @param bool $userId
     * @param bool $aclRole
     * @return array
     */
    public function getConfigDataProvider($apiKey = false, $userId = false, $aclRole = false)
    {
        $dataProvider = [
            "verifySSL" => false,
            "authGateway" => AbstractAdapter::AUTH_GATEWAY
        ];
        if ($apiKey) {
            $dataProvider['apiKey'] = getenv('RISEART_TESTS_APPLICATION_USER_AUTH_API_KEY');
        }
        if ($userId) {
            $dataProvider['userId'] = getenv('RISEART_TESTS_APPLICATION_USER_AUTH_USER_ID');
        }
        if ($userId) {
            $dataProvider['aclRole'] = getenv('RISEART_TESTS_APPLICATION_USER_AUTH_ACL_ROLE');
        }

        return $dataProvider;
    }


}