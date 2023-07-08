<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\Txn\Coris\Core;

interface CredentialsInterface
{
    /**
     * Returns the authorization client secret/token.
     *
     * @return string|null
     */
    public function getApiToken();

    /**
     * Returns the authorization client key.
     *
     * @return string|null
     */
    public function getApiKey();
}
