<?php

namespace Drewlabs\Txn\Coris;

interface EndpointsInterface
{
    /**
     * Returns Server OTP endpoint
     * 
     * @return string 
     */
    public function forOTP(): string;

    /**
     * Returns TXN Payment endpoint
     * 
     * @return string 
     */
    public function forTxnPayment(): string;

    /**
     * Returns path to query for client informations
     * 
     * @return string 
     */
    public function forClientInfo(): string;

    /**
     * Returns path to create hash values
     * 
     * @return string|null 
     */
    public function forHash();
}