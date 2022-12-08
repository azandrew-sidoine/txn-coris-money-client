<?php

use Drewlabs\Txn\Coris\Core\CorisGlobals;
use Drewlabs\Txn\Coris\Core\Credentials;
use Drewlabs\Txn\Coris\Core\CredentialsInterface;
use PHPUnit\Framework\TestCase;

class CorisGlobalsTest extends TestCase
{

    private function getInstance()
    {
        return CorisGlobals::getInstance();
    }

    public function test_coris_global_set_credentials_factory_set_the_credentials_instance()
    {
        $this->getInstance()->setCredentialsFactory(function() {
            return new Credentials('keyid', 'SuperKeySecret');
        });
        $this->assertInstanceOf(CredentialsInterface::class, $this->getInstance()->getCredentials());
    }

    public function test_coris_global_set_credentials_factory_set_the_api_token_value()
    {
        $this->getInstance()->setCredentialsFactory(function() {
            return new Credentials('keyid', 'SuperKeySecret');
        });
        $this->assertEquals('SuperKeySecret', $this->getInstance()->getCredentials()->getApiToken());
    }

    public function test_coris_global_set_credentials_factory_set_the_api_key_value()
    {
        $this->getInstance()->setCredentialsFactory(function() {
            return new Credentials('keyid', 'SuperKeySecret');
        });
        $this->assertEquals('keyid', $this->getInstance()->getCredentials()->getApiKey());
    }

    public function test_coris_global_set_code_pv()
    {
        $this->getInstance()->codePv('X7924-L9742');
        $this->assertEquals('X7924-L9742', $this->getInstance()->codePv());
    }
}