<?php

use Drewlabs\Libman\LibraryConfig;
use Drewlabs\Libman\WebserviceLibraryConfig;
use Drewlabs\Txn\Coris\Client;
use Drewlabs\Txn\Coris\Core\CorisGlobals;
use Drewlabs\Txn\Coris\Core\Credentials;
use Drewlabs\Txn\Coris\Factory;
use Drewlabs\Txn\ProcessorLibraryInterface;
use PHPUnit\Framework\TestCase;

class ClientFactoryTest extends TestCase
{

    public function test_client_factory_create_instance_method()
    {
        $factory = new Factory;
        $client = $factory->createInstance(
            new WebserviceLibraryConfig(
                'coris-monet-client',
                'composer',
                'https://testbed.corismoney.com',
                'Test',
                'Test'
            )
        );
        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(ProcessorLibraryInterface::class, $client);
        $this->assertEquals($client->getApiClient(), 'Test');
        $this->assertEquals($client->getApiToken(), 'Test');
    }

    public function test_factory_create_instance_with_basic_library_config()
    {
        // Set the resolver to return empty credentials
        $factory = new Factory;
        $client = $factory->createInstance(
            LibraryConfig::new(
                'coris-monet-client',
                'composer'
            )
        );
        CorisGlobals::getInstance()->setCredentialsFactory(function() {
            return Credentials::empty();
        });
        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(ProcessorLibraryInterface::class, $client);
        $this->assertTrue(is_null($client->getApiClient()));
        $this->assertTrue(is_null($client->getApiToken()));
    }
}