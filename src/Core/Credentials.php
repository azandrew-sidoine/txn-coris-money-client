<?php

namespace Drewlabs\Txn\Coris\Core;

use ReflectionException;

class Credentials implements CredentialsInterface
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $token;

    /**
     * Creates a credentials instance
     * 
     * @param string $id        API Client
     * @param string $secret    API Token
     */
    public function __construct($key, string $secret)
    {
        $this->key = $key;
        $this->token = $secret;
    }

    /**
     * Creates an instance of {@see \Drewlabs\Txn\Coris\Credentials} with key and token
     * properties having default (null) values
     * 
     * @return self 
     * @throws ReflectionException 
     */
    public static function empty()
    {
        $instance = (new \ReflectionClass(__CLASS__))->newInstanceWithoutConstructor();
        return $instance;
    }

    public function setApiKey(string $value)
    {
        $this->key = $value;
        return $this;
    }

    public function setApiToken(string $value)
    {
        $this->token = $value;
        return $this;
    }

    public function getApiToken()
    {
        return $this->token;
    }

    public function getApiKey()
    {
        return $this->key;
    }
}
