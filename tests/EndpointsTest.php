<?php

use Drewlabs\Txn\Coris\Endpoints;
use Drewlabs\Txn\Coris\EndpointsInterface;
use PHPUnit\Framework\TestCase;

class EndpointsTest extends TestCase
{

    public function test_endpoints_contructor_runs_wihtout_error()
    {
        $endpoints = new Endpoints('/api/external/payment', '/api/external/otp', '/api/external/users');
        $this->assertInstanceOf(EndpointsInterface::class, $endpoints);
    }

    public function test_endpoints_getters_methods()
    {
        $endpoints = new Endpoints('/api/external/payment', '/api/external/otp', '/api/external/clients');
        $this->assertEquals('/api/external/payment', $endpoints->forTxnPayment());
        $this->assertEquals('/api/external/otp', $endpoints->forOTP());
        $this->assertEquals('/api/external/clients', $endpoints->forClientInfo());
        $this->assertEquals(null, $endpoints->forHash());
    }

    public function test_default_endpoints_getters()
    {
        $endpoints = Endpoints::defaults();
        $this->assertEquals('/external/v1/api/operations/paiement-bien', $endpoints->forTxnPayment());
        $this->assertEquals('/external/v1/api/send-code-otp', $endpoints->forOTP());
        $this->assertEquals('/external/v1/api/infos-client', $endpoints->forClientInfo());
        $this->assertEquals('/external/v1/api/hash256', $endpoints->forHash());
    }
}