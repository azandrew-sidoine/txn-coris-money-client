<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Drewlabs\Libman\LibraryConfig;
use Drewlabs\Libman\WebserviceLibraryConfig;
use Drewlabs\Txn\Coris\Client;
use Drewlabs\Txn\Coris\Core\Credentials;
use Drewlabs\Txn\Coris\Factory;
use Drewlabs\Txn\ProcessorLibraryInterface;
use PHPUnit\Framework\TestCase;

class ClientFactoryTest extends TestCase
{
    public function test_client_factory_create_instance_method()
    {
        $factory = new Factory();
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
        $this->assertSame($client->getApiClient(), 'Test');
        $this->assertSame($client->getApiToken(), 'Test');
    }

    public function test_factory_create_instance_with_basic_library_config()
    {
        // Set the resolver to return empty credentials
        $factory = new Factory();
        $client = $factory->createInstance(LibraryConfig::new('coris-monet-client', 'composer'));
        $client->setCredentialsFactory(static fn () => Credentials::empty());
        $this->assertInstanceOf(Client::class, $client);
        $this->assertInstanceOf(ProcessorLibraryInterface::class, $client);
        $this->assertTrue(null === $client->getApiClient());
        $this->assertTrue(null === $client->getApiToken());
    }
}
