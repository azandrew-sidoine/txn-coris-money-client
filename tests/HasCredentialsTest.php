<?php

use Drewlabs\Txn\Coris\Credentials;
use Drewlabs\Txn\Coris\CredentialsInterface;
use Drewlabs\Txn\Coris\Tests\HasCredentialsStub;
use PHPUnit\Framework\TestCase;

class HasCredentialsTest extends TestCase
{

    public function test_has_credentials_get_credentials()
    {
        $object = new HasCredentialsStub;
        $object->setCredentials(new Credentials('keyid', 'SuperKeySecret'));
        $this->assertInstanceOf(CredentialsInterface::class, $object->getCredentials());
    }

    public function test_has_credentials_get_api_token()
    {
        $object = new HasCredentialsStub;
        $object->setCredentials(new Credentials('keyid', 'SuperKeySecret'));
        $this->assertEquals('SuperKeySecret', $object->getApiToken());
    }

    public function test_has_credentials_get_api_client()
    {
        $object = new HasCredentialsStub;
        $object->setCredentials(new Credentials('keyid', 'SuperKeySecret'));
        $this->assertEquals('keyid', $object->getApiClient());
    }
}