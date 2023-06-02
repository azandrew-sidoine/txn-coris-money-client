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

namespace Drewlabs\Txn\Coris\Core;

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
     * Creates a credentials instance.
     *
     * @param string $secret API Token
     */
    public function __construct($key, string $secret)
    {
        $this->key = $key;
        $this->token = $secret;
    }

    /**
     * Creates an instance of {@see \Drewlabs\Txn\Coris\Credentials} with key and token
     * properties having default (null) values.
     *
     * @throws \ReflectionException
     *
     * @return self
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
