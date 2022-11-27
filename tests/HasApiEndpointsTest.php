<?php

use Drewlabs\Txn\Coris\Endpoints;
use Drewlabs\Txn\Coris\Tests\HasApiEndpointsStub;
use PHPUnit\Framework\TestCase;

class HasApiEndpointsTest extends TestCase
{
    public function test_has_api_endpoints_get_endpoints()
    {
        $object = new HasApiEndpointsStub;
        $object->useDefaultEndpoints();
        $this->assertEquals('/external/v1/api/operations/paiement-bien', $object->getEndpoints()->forTxnPayment());
        $this->assertEquals('/external/v1/api/send-code-otp', $object->getEndpoints()->forOTP());
        $this->assertEquals('/external/v1/api/infos-client', $object->getEndpoints()->forClientInfo());
        $this->assertEquals('/external/v1/api/hash256', $object->getEndpoints()->forHash());
    }

    public function test_has_api_endpoints_set_endpoints()
    {
        $object = new HasApiEndpointsStub;
        $object->useDefaultEndpoints();
        $endpoints = new Endpoints('/api/external/payment', '/api/external/otp', '/api/external/clients');
        $object->setEndpoints($endpoints);
        $this->assertEquals('/api/external/payment', $object->getEndpoints()->forTxnPayment());
        $this->assertEquals('/api/external/otp', $object->getEndpoints()->forOTP());
        $this->assertEquals('/api/external/clients', $object->getEndpoints()->forClientInfo());
        $this->assertEquals(null, $object->getEndpoints()->forHash());
    }
}