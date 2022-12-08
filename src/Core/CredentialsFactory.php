<?php

namespace Drewlabs\Txn\Coris\Core;

interface CredentialsFactory
{

    /**
     * Creates client credentials instance
     * 
     * @return CredentialsInterface 
     */
    public function __invoke(): CredentialsInterface;
}