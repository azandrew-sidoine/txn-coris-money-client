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

use Drewlabs\Txn\Coris\Core\ClientInfo;
use Drewlabs\Txn\Exceptions\InvalidProcessorOTPException;
use Drewlabs\Txn\Exceptions\MissingClientAccountException;
use Drewlabs\Txn\Exceptions\ProcessTxnRequestException;
use Drewlabs\Txn\Exceptions\RequestException;
use Drewlabs\Txn\TransactionalPaymentInterface;
use Drewlabs\Txn\TransactionPaymentInterface;

/**
 * @mixin ConfigRespositoryAware
 */
trait InteractsWithServer
{

    /**
     * @var string
     */
    private $host;

    use HasApiCredentials;
    use HasApiEndpoints;
    use ParsesResponse;

    public function requestOTP(string $payeerid)
    {
        [$iso, $number] = $this->splitPayeerId($payeerid);
        if ((null === $iso) || (null === $number)) {
            throw new \UnexpectedValueException("Payeer id $payeerid is not valid. Payeer id must be in form of (isocode phonenumber) or (isocode-phonnumber) in order to be valid");
        }

        $this->getClientInfo($iso, $number, $hash = $this->computeHash(sprintf('%s%s%s', $iso, $number, $this->getApiToken())));

        $this->resetCurl();

        // We create the REST endpoint by appending the request query to the request path
        $endpoint = $this->getEndpoints()->forOTP() . '?' . http_build_query([
            'codePays' => $iso,
            'telephone' => $number,
        ], '', '&', \PHP_QUERY_RFC3986);

        // Sends the request to the coris webservice host
        $this->curl->send([
            'method' => 'POST',
            'url' => ($url = $this->getRequestURL($endpoint)),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'hashParam' => $hash,
                'clientId' => $this->getApiClient(),
            ],
        ]);
        if (200 !== ($statusCode = $this->curl->getStatusCode())) {
            throw new RequestException("/GET $url : Unknown Request error", $statusCode);
        }
        $response = $this->decodeRequestResponse(
            $this->curl->getResponse(),
            $this->parseHeaders($this->curl->getResponseHeaders())
        );
        if (!\is_array($response)) {
            throw new RequestException("/GET $url : Server return a bad response");
        }
        if (null !== ($text = ($response['text'] ?? null)) && \is_string($text)) {
            return true;
        }
        throw new RequestException("/GET $url : " . $response['msg'] ?? 'Unkown request error');
    }

    /**
     * Query for a client info on coris money platform.
     *
     * {@inheritDoc}
     *
     * @throws RequestException
     * @throws RequestException
     * @throws MissingClientAccountException
     * @throws MissingClientAccountException
     *
     * @return ClientInfo
     */
    public function getClientInfo(string $iso, string $number, string $hash = null)
    {
        $hash = null !== $hash ? $hash : $this->computeHash(sprintf('%s%s%s', $iso, $number, $this->getApiToken()));

        $this->resetCurl();

        // We create the REST endpoint by appending the request query to the request path
        $endpoint = $this->getEndpoints()->forClientInfo() . '?' . http_build_query([
            'codePays' => $iso,
            'telephone' => $number,
        ], '', '&', \PHP_QUERY_RFC3986);

        // Sends the request to the coris webservice host
        $this->curl->send([
            'method' => 'GET',
            'url' => ($url = $this->getRequestURL($endpoint)),
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'hashParam' => $hash,
                // TODO : Provide a better implementation of querying the client id,
                'clientId' => $this->getApiClient(),
            ],
        ]);
        if (200 !== ($statusCode = $this->curl->getStatusCode())) {
            throw new RequestException("/GET $url : Unknown Request error", $statusCode);
        }
        $response = $this->decodeRequestResponse(
            $this->curl->getResponse(),
            $this->parseHeaders($this->curl->getResponseHeaders())
        );
        if (1 === (int) ($response['code'] ?? null)) {
            throw new RequestException("/GET $url: " . ($response['message'] ?? $response['msg'] ?? 'Unknown request error'));
        }
        if ((-1 === (int) ($response['code'] ?? null)) || (false !== strstr($response['message'] ?? $response['msg'] ?? '', 'client inexistant'))) {
            throw new MissingClientAccountException("/GET $url: " . ($response['message'] ?? $response['msg'] ?? 'Unknown request error'));
        }
        if (null !== ($text = ($response['text'] ?? null)) && \is_string($text)) {
            return ClientInfo::create(simplexml_load_string($text));
        }

        throw new RequestException("/GET $url : " . ($response['msg'] ?? $response['message'] ?? 'Unkown request error'));
    }

    /**
     * Send a request to corisMoney /POST /external/v1/api/operations/paiement-bien
     * to process the txn.
     *
     * @throws \UnexpectedValueException
     * @throws RequestException
     * @throws ProcessTxnRequestException
     *
     * @return true
     */
    public function doProcessTxnPayment(TransactionPaymentInterface $transaction)
    {
        [$iso, $number] = $this->resolvePayeerRequiredAttributes($transaction);
        /**
         * @var TransactionalPaymentInterface&TransactionPaymentInterface
         */
        $txn = $transaction;
        $hash = $this->computeHash(
            sprintf(
                '%s%s%s%s%s%s',
                $iso,
                $number,
                /* Check if the codePV is not the transaction reference */
                $accountPvCode = $this->getSalePoint() /* Code PV */,
                $amount = $txn->getValue(),
                $otp = $txn->getOTP(),
                $this->getApiToken()
            )
        );
        $this->resetCurl();
        // We create the REST endpoint by appending the request query to the request path
        $endpoint = $this->getEndpoints()->forTxnPayment() . '?' . http_build_query([
            'codePays' => $iso,
            'telephone' => $number,
            'codePv' => $accountPvCode,
            'montant' => $amount,
            'codeOTP' => $otp,
        ], '', '&', \PHP_QUERY_RFC3986);

        // Sends the request to the coris webservice host
        $this->curl->send([
            'method' => 'POST',
            'url' => ($url = $this->getRequestURL($endpoint)),
            'headers' => [
                'Content-Type' => 'application/json',
                'hashParam' => $hash,
                'clientId' => $this->getApiClient(),
            ],
        ]);
        if (200 !== ($statusCode = $this->curl->getStatusCode())) {
            throw new RequestException("/POST $url : Unknown Request error", $statusCode);
        }
        $response = $this->decodeRequestResponse(
            $this->curl->getResponse(),
            $this->parseHeaders($this->curl->getResponseHeaders())
        );
        if (!\is_array($response)) {
            throw new RequestException("/POST $url : Server return a bad response");
        }

        if (1 === (int) ($response['code'] ?? null)) {
            throw new ProcessTxnRequestException($txn, "/POST $url: " . $response['message'] ?? $response['msg'] ?? 'Unknown request error');
        }

        if ((-1 === (int) ($response['code'] ?? null)) || (false !== strstr($response['message'] ?? $response['msg'] ?? '', 'OTP Incorrect'))) {
            throw new InvalidProcessorOTPException($response['message'] ?? $response['msg'] ?? 'Unknown request error');
        }
        if ((null === ($response['code'] ?? null)) || (null === ($response['transactionId'] ?? null))) {
            throw new RequestException("/GET $url : " . ($response['msg'] ?? $response['message'] ?? 'Unkown request error'));
        }
        $result = $this->toProcessTransactionResult(array_merge($response ?? [], ['payment' => $txn]));
        if (!empty($this->responseListeners)) {
            /**
             * @var TransactionResultListener $callback
             */
            foreach ($this->responseListeners as $callback) {
                $callback($result);
            }
        }

        return true;
    }

    /**
     * Creates a hash string from a plain text string.
     *
     * @throws RequestException
     *
     * @return string
     */
    public function createHashString(string $plainText)
    {
        // Case the hash endpoint is not provided we use the default function
        // for computing hash string
        if (null === ($hash = $this->getEndpoints()->forHash())) {
            return $this->computeHash($plainText);
        }
        $endpoint = $hash . "?originalString=$plainText";
        $this->resetCurl();
        $this->curl->send([
            'method' => 'GET',
            'url' => ($url = $this->getRequestURL($endpoint)),
            'headers' => [
                'clientId' => $this->getApiClient(),
            ],
        ]);
        if (200 !== ($statusCode = $this->curl->getStatusCode())) {
            throw new RequestException("/GET $url : Request error", $statusCode);
        }
        // The response contains the raw string for the current request
        if (empty($response = $this->curl->getResponse())) {
            throw new RequestException("/GET $url : Bad Response, expected response to be a valid PHP string");
        }

        return $response;
    }

    /**
     * @return string[]
     */
    private function splitPayeerId(string $payeerid)
    {
        $exploded = false !== stripos($payeerid, '-') ? explode('-', $payeerid, 2) : explode(' ', $payeerid);
        if (2 === \count($exploded)) {
            [$iso_code, $phone_number] = $exploded;
            $iso_code = '+' === $iso_code[0] ? substr($iso_code, 1) : ('00' === substr($iso_code, 0, 2) ? substr($iso_code, 2) : "$iso_code");

            return [$iso_code, $phone_number];
        }

        return [null, $exploded[0]];
    }

    /**
     * @throws \UnexpectedValueException
     *
     * @return array
     */
    private function resolvePayeerRequiredAttributes(TransactionPaymentInterface $transaction)
    {
        $payeerid = $transaction->getFrom();
        [$iso, $number] = $this->splitPayeerId($payeerid);
        if ((null === $iso) || (null === $number)) {
            throw new \UnexpectedValueException("Payeer id $payeerid is not valid. Payeer id must be in form of (isocode phonenumber) or (isocode-phonnumber) in order to be valid");
        }
        if (!($transaction instanceof TransactionalPaymentInterface)) {
            throw new \UnexpectedValueException('Cannot process transaction: ' . $transaction->getId());
        }

        return [$iso, $number, $payeerid];
    }

    private function getSalePoint()
    {
        // The algorithm search for salePoint -> sale_point -> code_pv values in the configuration
        return $this->getConfig('salePoint', function () {
            return $this->getConfig('sale_point', function () {
                return $this->getConfig('code_pv');
            });
        });
    }

    /**
     * return the request url for the given path
     * 
     * @param string $path 
     * @return string 
     */
    private function getRequestURL(string $path)
    {
        return rtrim($this->host, '/') . (empty($path) ? '' : ('/' . ltrim($path, '/')));
    }
}
