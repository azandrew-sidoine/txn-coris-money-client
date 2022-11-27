<?php

namespace Drewlabs\Txn\Coris;

interface CredentialsInterface
{
    /**
     * Returns the authorization client secret/token
     * 
     * @return string 
     */
    public function getApiToken(): string;

    /**
     * Returns the authorization client key
     * 
     * @return string 
     */
    public function getApiKey(): string;
}