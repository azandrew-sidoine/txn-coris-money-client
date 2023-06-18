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

namespace Drewlabs\Txn\Coris;

use Drewlabs\Libman\Contracts\AuthBasedLibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryFactoryInterface;
use Drewlabs\Libman\Contracts\WebServiceLibraryConfigInterface;
use Drewlabs\Txn\Coris\Core\Credentials;

class Factory implements LibraryFactoryInterface
{
    /**
     * Creates an instance of Coris client class.
     *
     * {@inheritDoc}
     *
     * @return Client
     */
    public static function createInstance(LibraryConfigInterface $config)
    {
        $hostname = ($config instanceof WebServiceLibraryConfigInterface) ? $config->getHost() : $config->getConfiguration()->get('api.host');

        // Create new client instance
        $client =  new Client($hostname, Endpoints::defaults());

        // Set the authorization / authentication credentials
        if (($config instanceof AuthBasedLibraryConfigInterface) && ($auth = $config->getAuth())) {
            $client->setCredentialsFactory(function () use ($auth) {
                return new Credentials($auth->id(), $auth->secret());
            });
        } else {
            // else we create the credentials factory from configuration values
            list($apiKey, $apiToken) = [$config->getConfiguration()->get('credentials.name', $config->getConfiguration()->get('api.credentials.name')), $config->getConfiguration()->get('credentials.token', $config->getConfiguration()->get('api.credentials.token'))];
            if ((null !== $apiKey) && (null !== $apiToken)) {
                $client->setCredentialsFactory(function () use ($apiKey, $apiToken) {
                    return new Credentials($apiKey, $apiToken);
                });
            }
        }

        // Set the configuration repository instance for the client instance
        $client = $client->setConfigRepository($config->getConfiguration());

        // Return the contructed client
        return $client;
    }
}
