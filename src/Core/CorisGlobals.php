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

/**
 * The credential global class allow library users to configure a credentials resolver instance
 * that might be used when making request to coris servers. It uses a singleton pattern to configure
 * and load required configurations.
 */
class CorisGlobals
{
    /**
     * @var static
     */
    private static $instance;

    /**
     * @var CredentialsInterface
     */
    private $factory;

    /**
     * @var mixed
     */
    private $credentials;

    /**
     * @var int|string
     */
    private $codePv = '0053743089';

    /**
     * @var int|string
     */
    private $codeUO = '';

    /**
     * Makes the contructor private to avoid instanciation.
     */
    private function __construct()
    {
        throw new \RuntimeException('This is a singleton object, use the getInstance() static method to get an instance of the class');
    }

    /**
     * Resolve a singleton intance of the class.
     *
     * @throws \ReflectionException
     *
     * @return self
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = (new \ReflectionClass(__CLASS__))->newInstanceWithoutConstructor();
        }

        return self::$instance;
    }

    /**
     * Set the credentials factory function.
     */
    public function setCredentialsFactory(callable $factory)
    {
        // Each time the credential factory is updated, we reset the value of the credentials
        // property
        $this->factory = $factory;
        if (null !== $this->credentials) {
            $this->credentials = null;
        }
    }

    /**
     * Returns the value of client credentials.
     *
     * @throws \UnexpectedValueException
     *
     * @return CredentialsInterface
     */
    public function getCredentials()
    {
        if (null === $this->credentials) {
            // We pass the current instace to the factory function
            // in case the developper will require the global instance
            $credentials = ($this->factory)($this);
            if (!($credentials instanceof CredentialsInterface)) {
                throw new \UnexpectedValueException('Provided credentials factory must return instance of '.CredentialsInterface::class.', got '.((null !== $credentials) && \is_object($credentials) ? $credentials::class : \gettype($credentials)));
            }
            $this->credentials = $credentials;
        }

        return $this->credentials;
    }

    /**
     * Set the coris money global pv code configuration value.
     *
     * @param string|int|null $value
     *
     * @throws \InvalidArgumentException
     *
     * @return int|string
     */
    public function codePv($value = null)
    {
        if (null !== $value) {
            if (!(\is_int($value) || \is_string($value))) {
                throw new \InvalidArgumentException('Expect 1st parameter to be a scalar value');
            }
            $this->codePv = $value;
        }

        return $this->codePv;
    }

    /**
     * Set the Coris money global uo code configuration value.
     *
     * @param string|int|null $value
     *
     * @throws \InvalidArgumentException
     *
     * @return int|string
     */
    public function codeUO($value = null)
    {
        if (null !== $value) {
            if (!(\is_int($value) || \is_string($value))) {
                throw new \InvalidArgumentException('Expect 1st parameter to be a scalar value');
            }
            $this->codeUO = $value;
        }

        return $this->codeUO;
    }
}
