<?php

namespace Drewlabs\Txn\Coris;

use Drewlabs\Libman\Contracts\AuthBasedLibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryConfigInterface;
use Drewlabs\Libman\Contracts\LibraryFactoryInterface;
use Drewlabs\Libman\Contracts\WebServiceLibraryConfigInterface;
use Drewlabs\Txn\Coris\Client;
use Drewlabs\Txn\Coris\Credentials;
use Drewlabs\Txn\Coris\Endpoints;

class Factory implements LibraryFactoryInterface
{

    /**
     * Creates an instance of Coris client class
     * 
     * {@inheritDoc}
     * @param LibraryConfigInterface $config
     * 
     * @return Client
     */
    public static function createInstance(LibraryConfigInterface $config)
    {
        $credentials = ($config instanceof AuthBasedLibraryConfigInterface) && ($auth = $config->getAuth()) ? new Credentials($auth->id(), $auth->secret()) : Credentials::empty();
        $host = ($config instanceof WebServiceLibraryConfigInterface) ? $config->getHost() : null;
        return new Client($host, $credentials, Endpoints::defaults());
    }
}
