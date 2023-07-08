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

use Drewlabs\Txn\Coris\Endpoints;
use Drewlabs\Txn\Coris\Tests\HasApiEndpointsStub;
use PHPUnit\Framework\TestCase;

class HasApiEndpointsTest extends TestCase
{
    public function test_has_api_endpoints_get_endpoints()
    {
        $object = new HasApiEndpointsStub();
        $object->useDefaultEndpoints();
        $this->assertSame('/external/v1/api/operations/paiement-bien', $object->getEndpoints()->forTxnPayment());
        $this->assertSame('/external/v1/api/send-code-otp', $object->getEndpoints()->forOTP());
        $this->assertSame('/external/v1/api/infos-client', $object->getEndpoints()->forClientInfo());
        $this->assertSame('/external/v1/api/hash256', $object->getEndpoints()->forHash());
    }

    public function test_has_api_endpoints_set_endpoints()
    {
        $object = new HasApiEndpointsStub();
        $object->useDefaultEndpoints();
        $endpoints = new Endpoints('/payment', '/otp', '/clients');
        $endpoints->setBasePath('/api/external');
        $object->setEndpoints($endpoints);
        $this->assertSame('/api/external/payment', $object->getEndpoints()->forTxnPayment());
        $this->assertSame('/api/external/otp', $object->getEndpoints()->forOTP());
        $this->assertSame('/api/external/clients', $object->getEndpoints()->forClientInfo());
        $this->assertSame('/api/external/', $object->getEndpoints()->forHash());
    }
}
