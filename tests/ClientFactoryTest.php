<?php

use Drewlabs\Libman\LibraryConfig;
use Drewlabs\Libman\WebserviceLibraryConfig;
use Drewlabs\Txn\Coris\Client;
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
                '$2a$10$JpGsCNuqTznfONRCNRPZCeVjVkztgMoE32RHoCvAabImznwPN2NXS',
                'CNSS'
            )
        );
        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(ProcessorLibraryInterface::class, $client);
        $this->assertEquals($client->getApiClient(), 'CNSS');
        $this->assertEquals($client->getApiToken(), '$2a$10$JpGsCNuqTznfONRCNRPZCeVjVkztgMoE32RHoCvAabImznwPN2NXS');
    }

    public function test_factory_create_instance_with_basic_library_config()
    {
        $factory = new Factory;
        $client = $factory->createInstance(
            LibraryConfig::new(
                'coris-monet-client',
                'composer'
            )
        );
        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(ProcessorLibraryInterface::class, $client);
        $this->assertTrue(is_null($client->getApiClient()));
        $this->assertTrue(is_null($client->getApiToken()));
    }
}