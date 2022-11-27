<?php

namespace Drewlabs\Txn\Coris;

class Credentials implements CredentialsInterface
{
    /**
     * 
     * @var string
     */
    private $id;

    /**
     * 
     * @var string
     */
    private $token;

    /**
     * Creates a credentials instance
     * 
     * @param string $id 
     * @param string $secret 
     */
    public function __construct($id, string $secret)
    {
        $this->id = $id;
        $this->token = $secret;
    }

    public function getApiToken(): string
    {
        return $this->token;
    }

    public function getApiKey(): string
    {
        return $this->id;
    }
}
