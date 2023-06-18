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

namespace Drewlabs\Txn\Coris;

use Drewlabs\Txn\Coris\Core\CredentialsInterface;

trait HasApiCredentials
{

    /**
     * @var \Closure($client = null): CredentialsInterface
     */
    private $credentialsFactory;

    /**
     * @var mixed
     */
    private $credentials;

    /**
     * set credentials factory instance
     * 
     * @param callable $factory 
     * @return static 
     */
    public function setCredentialsFactory(callable $factory)
    {
        // Each time the credential factory is updated, we reset the value of the credentials
        // property
        $this->credentialsFactory = $factory;
        if (null !== $this->credentials) {
            $this->credentials = null;
        }
        return $this;
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
            $credentials = ($this->credentialsFactory)($this);
            if (!($credentials instanceof CredentialsInterface)) {
                throw new \UnexpectedValueException('Provided credentials factory must return instance of '.CredentialsInterface::class.', got '.((null !== $credentials) && \is_object($credentials) ? $credentials::class : \gettype($credentials)));
            }
            $this->credentials = $credentials;
        }

        return $this->credentials;
    }

    /**
     * Returns the client api token credential.
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->getCredentials()->getApiToken();
    }

    /**
     * Returns the client api key credential.
     *
     * @return string
     */
    public function getApiClient()
    {
        return $this->getCredentials()->getApiKey();
    }
}
