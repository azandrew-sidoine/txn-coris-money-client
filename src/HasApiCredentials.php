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

use Drewlabs\Txn\Coris\Core\CorisGlobals;
use Drewlabs\Txn\Coris\Core\CredentialsInterface;

trait HasApiCredentials
{
    /**
     * Getter for the credentials property of this instance.
     *
     * @return CredentialsInterface
     */
    public function getCredentials()
    {
        return CorisGlobals::getInstance()->getCredentials();
    }

    /**
     * Returns the client api token credential.
     *
     * @return string
     */
    public function getApiToken()
    {
        return $this->getCredentials()
            ->getApiToken();
    }

    /**
     * Returns the client api key credential.
     *
     * @return string
     */
    public function getApiClient()
    {
        return $this->getCredentials()
            ->getApiKey();
    }
}
