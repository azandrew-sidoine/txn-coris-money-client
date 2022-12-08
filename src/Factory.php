<?php

namespace Drewlabs\Txn\Coris;

use Drewlabs\Libman\Contracts\AuthBasedLibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryFactoryInterface;
use Drewlabs\Libman\Contracts\WebServiceLibraryConfigInterface;
use Drewlabs\Txn\Coris\Client;
use Drewlabs\Txn\Coris\Core\CorisGlobals;
use Drewlabs\Txn\Coris\Core\Credentials;
use Drewlabs\Txn\Coris\Endpoints;

class Factory implements LibraryFactoryInterface
{

    /**
     * Creates an instance of Coris client class
     * 
     * {@inheritDoc}
     * 
     * @param LibraryConfigInterface $config
     * 
     * @return Client
     */
    public static function createInstance(LibraryConfigInterface $config)
    {
        if (($config instanceof AuthBasedLibraryConfigInterface) && ($auth = $config->getAuth())) {
            CorisGlobals::getInstance()->setCredentialsFactory(function () use ($auth) {
                return  new Credentials($auth->id(), $auth->secret());
            });
        }
        return new Client(
            ($config instanceof WebServiceLibraryConfigInterface) ? $config->getHost() : null,
            Endpoints::defaults()
        );
    }
}
