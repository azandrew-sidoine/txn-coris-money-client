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

use Drewlabs\Curl\Client as Curl;
use Drewlabs\Txn\OneWayTransactionProcessorInterface;
use Drewlabs\Txn\ProcessorLibraryInterface;
use Drewlabs\Txn\TransactionalProcessorLibraryInterface;
use Drewlabs\Txn\TransactionPaymentInterface;
use Drewlabs\Txn\TransactionResultListener;

class Client implements ProcessorLibraryInterface, OneWayTransactionProcessorInterface, TransactionalProcessorLibraryInterface
{
    use ConfigRespositoryAware;
    use InteractsWithServer;

    /** @var array List of transaction response listeners. */
    private $responseListeners = [];

    /** @var Curl */
    private $curl;

    /**
     * Creates a {@see Client} instance.
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function __construct(?string $host = null, ?EndpointsInterface $endpoints = null)
    {
        $this->curl = new Curl();
        $this->curl->followLocation();
        $this->endpoints = $endpoints;
        $this->host = $host;
    }

    public function toProcessTransactionResult($response)
    {
        return new TransactionResult(
            $response['payment'] ?? null,
            $response['code'] ?? 1,
            $response['message'] ?? 'Unknown Error!',
            $response['transactionId'] ?? null,
            $response['created_at'] ?? null
        );
    }

    public function processTransaction(TransactionPaymentInterface $transaction)
    {
        return $this->doProcessTxnPayment($transaction);
    }

    public function addTransactionResponseLister($callback)
    {
        if ($callback instanceof \Closure || $callback instanceof TransactionResultListener) {
            $this->responseListeners[] = $callback;
        }
    }

    /**
     * Computes a hash string from the plain text value.
     *
     * @return string|false
     */
    public function computeHash(string $content)
    {
        return hash('sha256', $content, false);
    }

    /**
     * Reset the curl client to it default options.
     *
     * @return void
     */
    private function resetCurl()
    {
        // First we release the current client resources
        $this->curl->release();
        $this->curl->init();
        // Disable ssl verification to avoid any SSL error
        $this->curl->disableSSLVerification();
        $this->curl->setOption(\CURLOPT_RETURNTRANSFER, true);
    }
}
