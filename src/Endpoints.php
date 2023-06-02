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

class Endpoints implements EndpointsInterface
{
    /**
     * @var string
     */
    private $hashEndpoint;

    /**
     * @var string
     */
    private $otpEndpoint;

    /**
     * @var string
     */
    private $infoEndpoint;

    /**
     * @var string
     */
    private $paymentEndpoint;

    /**
     * Creates an instance of {@see \Drewlabs\Txn\Coris\Endpoints} class.
     *
     * @param string $hash
     */
    public function __construct(string $payment, string $otp, string $info, string $hash = null)
    {
        $this->hashEndpoint = $hash;
        $this->otpEndpoint = $otp;
        $this->paymentEndpoint = $payment;
        $this->infoEndpoint = $info;
    }

    /**
     * Creates an endpoint instance using default server paths.
     *
     * @return EndpointsInterface
     */
    public static function defaults()
    {
        return new self(
            '/external/v1/api/operations/paiement-bien',
            '/external/v1/api/send-code-otp',
            '/external/v1/api/infos-client',
            '/external/v1/api/hash256'
        );
    }

    public function forOTP(): string
    {
        return $this->otpEndpoint;
    }

    public function forTxnPayment(): string
    {
        return $this->paymentEndpoint;
    }

    public function forClientInfo(): string
    {
        return $this->infoEndpoint;
    }

    public function forHash()
    {
        return $this->hashEndpoint;
    }
}
