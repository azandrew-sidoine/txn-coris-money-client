<?php

namespace Drewlabs\Txn\Coris;

use Drewlabs\Txn\Coris\Core\ClientInfo;
use Drewlabs\Txn\Coris\Core\CorisGlobals;
use Drewlabs\Txn\Exceptions\InvalidProcessorOTPException;
use Drewlabs\Txn\Exceptions\MissingClientAccountException;
use Drewlabs\Txn\Exceptions\ProcessTxnRequestException;
use Drewlabs\Txn\Exceptions\RequestException;
use Drewlabs\Txn\TransactionalPaymentInterface;
use Drewlabs\Txn\TransactionPaymentInterface;
use UnexpectedValueException;

trait InteractsWithServer
{
    use HasApiEndpoints, HasApiCredentials, ParsesResponse;

    public function requestOTP(string $payeerid)
    {
        list($iso, $number) = $this->splitPayeerId($payeerid);
        if ((null === $iso) || (null === $number)) {
            throw new UnexpectedValueException("Payeer id $payeerid is not valid. Payeer id must be in form of (isocode phonenumber) or (isocode-phonnumber) in order to be valid");
        }
        $this->getClientInfo(
            $iso,
            $number,
            $hash = $this->createHashString(
                sprintf(
                    "%s%s%s",
                    $iso,
                    $number,
                    $this->getApiToken()
                )
            )
        );
        $this->resetCurl();

        // We create the REST endpoint by appending the request query to the request path
        $endpoint = $this->getEndpoints()->forOTP() . "?" . \http_build_query([
            'codePays' => $iso,
            'telephone' => $number
        ], '', '&', \PHP_QUERY_RFC3986);

        // Sends the request to the coris webservice host
        $this->curl->send([
            'method' => 'POST',
            'url' => $endpoint,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'hashParam' => $hash,
                'clientId' => $this->getApiClient()
            ]
        ]);
        if (200 !== ($statusCode = $this->curl->getStatusCode())) {
            throw new RequestException("/GET $endpoint : Unknown Request error", $statusCode);
        }
        $response = $this->decodeRequestResponse(
            $this->curl->getResponse(),
            $this->parseHeaders($this->curl->getResponseHeaders())
        );
        if (!is_array($response)) {
            throw new RequestException("/GET $endpoint : Server return a bad response");
        }
        if (null !== ($text = ($response['text'] ?? null)) && is_string($text)) {
            return true;
        }
        throw new RequestException("/GET $endpoint : " . $response['msg'] ?? 'Unkown request error');
    }

    /**
     * Query for a client info on coris money platform
     * 
     * {@inheritDoc}
     * 
     * @param string $iso 
     * @param string $number 
     * @param string|null $hash 
     * @return ClientInfo 
     * @throws RequestException 
     * @throws RequestException 
     * @throws MissingClientAccountException 
     * @throws MissingClientAccountException 
     */
    public function getClientInfo(string $iso, string $number, string $hash = null)
    {
        // TODO : Take a look a the documentation for the hash string when rerquesting OTP
        $hash = $hash ?? $this->createHashString(sprintf("%s%s%s", $iso, $number, $this->getApiToken()));
        // TODO : Get client information
        $this->resetCurl();

        // We create the REST endpoint by appending the request query to the request path
        $endpoint = $this->getEndpoints()->forClientInfo() . '?' . \http_build_query([
            'codePays' => $iso,
            'telephone' => $number
        ], '', '&', \PHP_QUERY_RFC3986);

        // Sends the request to the coris webservice host
        $this->curl->send([
            'method' => 'GET',
            'url' => $endpoint,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'hashParam' => $hash,
                // TODO : Provide a better implementation of querying the client id,
                'clientId' => $this->getApiClient()
            ]
        ]);
        if (200 !== ($statusCode = $this->curl->getStatusCode())) {
            throw new RequestException("/GET $endpoint : Unknown Request error", $statusCode);
        }
        $response = $this->decodeRequestResponse(
            $this->curl->getResponse(),
            $this->parseHeaders($this->curl->getResponseHeaders())
        );
        if (1 === intval($response['code'] ?? null)) {
            throw new RequestException("/GET $endpoint: " . ($response['message'] ?? $response['msg'] ?? 'Unknown request error'));
        }
        if ((-1 === intval($response['code'] ?? null)) || (false !== strstr($response['message'] ?? $response['msg'] ?? '', 'client inexistant'))) {
            throw new MissingClientAccountException("/GET $endpoint: " . ($response['message'] ?? $response['msg'] ?? 'Unknown request error'));
        }
        if ((null !== ($text = ($response['text'] ?? null)) && is_string($text))) {
            return ClientInfo::create(simplexml_load_string($text));
        }
        throw new RequestException("/GET $endpoint : " . ($response['msg'] ?? $response['message'] ?? 'Unkown request error'));
    }

    /**
     * Send a request to corisMoney /POST /external/v1/api/operations/paiement-bien
     * to process the txn
     * 
     * @param TransactionPaymentInterface $transaction 
     * @return true 
     * @throws UnexpectedValueException 
     * @throws RequestException 
     * @throws ProcessTxnRequestException 
     */
    public function doProcessTxnPayment(TransactionPaymentInterface $transaction)
    {
        list($iso, $number) = $this->resolvePayeerRequiredAttributes($transaction);
        /**
         * @var TransactionalPaymentInterface&TransactionPaymentInterface
         */
        $txn = $transaction;
        $hash = $this->createHashString(
            sprintf(
                "%s%s%s%s%s%s",
                $iso,
                $number,
                /* Check if the codePV is not the transaction reference */
                $accountPvCode = CorisGlobals::getInstance()->codePv() /* Code PV */,
                $amount = $txn->getValue(),
                $otp = $txn->getOTP(),
                $this->getApiToken()
            )
        );
        $this->resetCurl();
        // We create the REST endpoint by appending the request query to the request path
        $endpoint = $this->getEndpoints()->forTxnPayment() . "?" . \http_build_query([
            'codePays' => $iso,
            'telephone' => $number,
            'codePv' => $accountPvCode,
            'montant' => $amount,
            'codeOTP' => $otp
        ], '', '&', \PHP_QUERY_RFC3986);

        // Sends the request to the coris webservice host
        $this->curl->send([
            'method' => 'POST',
            'url' => $endpoint,
            'headers' => [
                'Content-Type' => 'application/json',
                'hashParam' => $hash,
                'clientId' => $this->getApiClient()
            ]
        ]);
        if (200 !== ($statusCode = $this->curl->getStatusCode())) {
            throw new RequestException("/POST $endpoint : Unknown Request error", $statusCode);
        }
        $response = $this->decodeRequestResponse(
            $this->curl->getResponse(),
            $this->parseHeaders($this->curl->getResponseHeaders())
        );
        if (!is_array($response)) {
            throw new RequestException("/POST $endpoint : Server return a bad response");
        }

        if (1 === intval($response['code'] ?? null)) {
            throw new ProcessTxnRequestException($txn, "/POST $endpoint: " . $response['message'] ?? $response['msg'] ?? 'Unknown request error');
        }

        if ((-1 === intval($response['code'] ?? null)) || (false !== strstr($response['message'] ?? $response['msg'] ?? '', 'OTP Incorrect'))) {
            throw new InvalidProcessorOTPException($response['message'] ?? $response['msg'] ?? 'Unknown request error');
        }
        if ((null === ($response['code'] ?? null)) || (null === ($response['transactionId'] ?? null))) {
            throw new RequestException("/GET $endpoint : " . ($response['msg'] ?? $response['message'] ?? 'Unkown request error'));
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
     * Creates a hash string from a plain text string
     * 
     * @param string $plainText 
     * @return string 
     * @throws RequestException 
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
            'url' => $endpoint,
            'headers' => [
                'clientId' => $this->getApiClient()
            ]
        ]);
        if (200 !== ($statusCode = $this->curl->getStatusCode())) {
            throw new RequestException("/GET $endpoint : Request error", $statusCode);
        }
        // The response contains the raw string for the current request
        if (empty($response = $this->curl->getResponse())) {
            throw new RequestException("/GET $endpoint : Bad Response, expected response to be a valid PHP string");
        }
        return $response;
    }


    /**
     * 
     * @param string $payeerid 
     * @return string[] 
     */
    private function splitPayeerId(string $payeerid)
    {
        $exploded = false !== stripos($payeerid, '-') ? explode('-', $payeerid, 2) : explode(' ', $payeerid);
        if (count($exploded) === 2) {
            list($iso_code, $phone_number) = $exploded;
            $iso_code = $iso_code[0] === '+' ? substr($iso_code, 1) : (substr($iso_code, 0, 2) === '00' ? substr($iso_code, 2) : "$iso_code");
            return [$iso_code, $phone_number];
        }
        return [null, $exploded[0]];
    }

    /**
     * 
     * @param TransactionPaymentInterface $transaction 
     * @return array 
     * @throws UnexpectedValueException 
     */
    private function resolvePayeerRequiredAttributes(TransactionPaymentInterface $transaction)
    {
        $payeerid = $transaction->getFrom();
        list($iso, $number) = $this->splitPayeerId($payeerid);
        if ((null === $iso) || (null === $number)) {
            throw new UnexpectedValueException("Payeer id $payeerid is not valid. Payeer id must be in form of (isocode phonenumber) or (isocode-phonnumber) in order to be valid");
        }
        if (!($transaction instanceof TransactionalPaymentInterface)) {
            throw new UnexpectedValueException("Cannot process transaction: " . $transaction->getId());
        }
        return [$iso, $number, $payeerid];
    }
}
