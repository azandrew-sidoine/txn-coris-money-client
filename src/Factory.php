<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
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
        $host = ($config instanceof WebServiceLibraryConfigInterface) ? ($config->getHost() ?? '') : ($config->getConfiguration()->get('api.host', '') ?? '');
        $hostname = sprintf('%s://%s', parse_url($host, \PHP_URL_SCHEME), parse_url($host, \PHP_URL_HOST));

        // Create new client instance

        // #region Set endpoints base path
        $endpoints = Endpoints::defaults();
        $endpoints->setBasePath($config->getConfiguration()->get('api.paths.base') ?? Defaults::API_228_BASE_PATH);
        // #endregion Set endpoints base path

        $client = new Client($hostname, $endpoints);

        // Set the authorization / authentication credentials
        if (($config instanceof AuthBasedLibraryConfigInterface) && ($auth = $config->getAuth())) {
            $client->setCredentialsFactory(static function () use ($auth) {
                return new Credentials($auth->id(), $auth->secret());
            });
        } else {
            // else we create the credentials factory from configuration values
            [$apiKey, $apiToken] = [$config->getConfiguration()->get('credentials.name') ?? $config->getConfiguration()->get('api.credentials.name'), $config->getConfiguration()->get('credentials.token') ?? $config->getConfiguration()->get('api.credentials.token')];
            if ((null !== $apiKey) && (null !== $apiToken)) {
                $client->setCredentialsFactory(static function () use ($apiKey, $apiToken) {
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
