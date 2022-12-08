<?php

use Drewlabs\Txn\Coris\Core\Credentials;
use Drewlabs\Txn\Coris\Core\CredentialsInterface as CoreCredentialsInterface;
use PHPUnit\Framework\TestCase;

class CredentialsTest extends TestCase
{

    public function test_credentials_constructor_executes_without_error()
    {
        $credentials = new Credentials('keyid', 'SuperSecret');
        $this->assertInstanceOf(CoreCredentialsInterface::class, $credentials);
    }

    public function test_credentials_get_api_key()
    {
        $credentials = new Credentials('keyid', 'SuperSecret');
        $this->assertEquals('keyid', $credentials->getApiKey());
    }

    public function test_credentials_get_api_token()
    {
        $credentials = new Credentials('keyid', 'SuperSecret');
        $this->assertEquals('SuperSecret', $credentials->getApiToken());
    }
}