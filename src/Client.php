<?php

namespace Drewlabs\Txn\Coris;

use Drewlabs\Txn\OneWayTransactionProcessorInterface;
use Drewlabs\Txn\ProcessorLibraryInterface;
use Drewlabs\Txn\TransactionalProcessorLibraryInterface;
use Drewlabs\Txn\TransactionPaymentInterface;
use Drewlabs\Curl\Client as Curl;
use Drewlabs\Txn\TransactionResultListener;
use InvalidArgumentException;

class Client implements
    ProcessorLibraryInterface,
    OneWayTransactionProcessorInterface,
    TransactionalProcessorLibraryInterface
{

    use InteractsWithServer;

    /**
     * List of transaction response listeners
     *
     * @var array
     */
    private $responseListeners = [];

    /**
     * 
     * @var Curl
     */
    private $curl;

    /**
     * Creates a {@see \Drewlabs\Txn\Coris\Client} instance
     * 
     * @param string|null $host 
     * @param EndpointsInterface|null $endpoints 
     * @return void 
     * @throws InvalidArgumentException 
     */
    public function __construct(
        string $host = null,
        EndpointsInterface $endpoints = null
    ) {
        $this->curl = new Curl($host);
        $this->endpoints = $endpoints;
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
     * Reset the curl client to it default options
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
        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);
    }

    /**
     * Computes a hash string from the plain text value
     * 
     * @param string $content 
     * @return string|false 
     */
    public function computeHash(string $content)
    {
        return hash('sha256', $content, false);
    }
}
