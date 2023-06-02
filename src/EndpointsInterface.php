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

interface EndpointsInterface
{
    /**
     * Returns Server OTP endpoint.
     */
    public function forOTP(): string;

    /**
     * Returns TXN Payment endpoint.
     */
    public function forTxnPayment(): string;

    /**
     * Returns path to query for client informations.
     */
    public function forClientInfo(): string;

    /**
     * Returns path to create hash values.
     *
     * @return string|null
     */
    public function forHash();
}
