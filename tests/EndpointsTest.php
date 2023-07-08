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
use Drewlabs\Txn\Coris\EndpointsInterface;
use PHPUnit\Framework\TestCase;

class EndpointsTest extends TestCase
{
    public function test_endpoints_contructor_runs_wihtout_error()
    {
        $endpoints = new Endpoints('payment', 'otp', 'users');
        $endpoints->setBasePath('api/external');
        $this->assertInstanceOf(EndpointsInterface::class, $endpoints);
    }

    public function test_endpoints_getters_methods()
    {
        $endpoints = new Endpoints('/payment', '/otp', '/clients');
        $endpoints->setBasePath('api/external');
        $this->assertSame('/api/external/payment', $endpoints->forTxnPayment());
        $this->assertSame('/api/external/otp', $endpoints->forOTP());
        $this->assertSame('/api/external/clients', $endpoints->forClientInfo());
        $this->assertSame('/api/external/', $endpoints->forHash());
    }

    public function test_default_endpoints_getters()
    {
        $endpoints = Endpoints::defaults();
        $this->assertSame('/external/v1/api/operations/paiement-bien', $endpoints->forTxnPayment());
        $this->assertSame('/external/v1/api/send-code-otp', $endpoints->forOTP());
        $this->assertSame('/external/v1/api/infos-client', $endpoints->forClientInfo());
        $this->assertSame('/external/v1/api/hash256', $endpoints->forHash());
    }
}
