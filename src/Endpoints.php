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
     * @var string
     */
    private $basePath;

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
        $this->basePath = Defaults::API_BASE_PATH;
    }

    /**
     * Creates an endpoint instance using default server paths.
     *
     * @return static
     */
    public static function defaults()
    {
        return new self('/operations/paiement-bien', '/send-code-otp', '/infos-client', '/hash256');
    }

    /**
     * set tbe base path for endpoints.
     *
     * @return static
     */
    public function setBasePath(string $path)
    {
        $this->basePath = $path;

        return $this;
    }

    public function forOTP(): string
    {
        $endpoint = trim($this->otpEndpoint ?? '', '/');
        $basePath = trim($this->basePath ?? '', '/');

        return sprintf('/%s/%s', $basePath, $endpoint);
    }

    public function forTxnPayment(): string
    {
        $endpoint = trim($this->paymentEndpoint ?? '', '/');
        $basePath = trim($this->basePath ?? '', '/');

        return sprintf('/%s/%s', $basePath, $endpoint);
    }

    public function forClientInfo(): string
    {
        $endpoint = trim($this->infoEndpoint ?? '', '/');
        $basePath = trim($this->basePath ?? '', '/');

        return sprintf('/%s/%s', $basePath, $endpoint);
    }

    public function forHash()
    {
        $endpoint = trim($this->hashEndpoint ?? '', '/');
        $basePath = trim($this->basePath ?? '', '/');

        return sprintf('/%s/%s', $basePath, $endpoint);
    }
}
