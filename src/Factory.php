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
use Drewlabs\Txn\Coris\Core\CorisGlobals;
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
        if (($config instanceof AuthBasedLibraryConfigInterface) && ($auth = $config->getAuth())) {
            CorisGlobals::getInstance()->setCredentialsFactory(static fn () => new Credentials($auth->id(), $auth->secret()));
        }

        return new Client(
            ($config instanceof WebServiceLibraryConfigInterface) ? $config->getHost() : null,
            Endpoints::defaults()
        );
    }
}
