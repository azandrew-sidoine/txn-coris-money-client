<?php

namespace Drewlabs\Txn\Coris;

interface CredentialsFactory
{

    /**
     * Creates client credentials instance
     * 
     * @return CredentialsInterface 
     */
    public function __invoke(): CredentialsInterface;
}