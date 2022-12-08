<?php

namespace Drewlabs\Txn\Coris\Core;

interface CredentialsInterface
{
    /**
     * Returns the authorization client secret/token
     * 
     * @return string|null
     */
    public function getApiToken();

    /**
     * Returns the authorization client key
     * 
     * @return string|null
     */
    public function getApiKey();
}