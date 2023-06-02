<?php

declare(strict_types=1);

/*
 * This file is part of the Drewlabs package.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Drewlabs\Txn\Coris\Core\CorisGlobals;
use Drewlabs\Txn\Coris\Core\Credentials;
use Drewlabs\Txn\Coris\Core\CredentialsInterface;
use PHPUnit\Framework\TestCase;

class CorisGlobalsTest extends TestCase
{
    public function test_coris_global_set_credentials_factory_set_the_credentials_instance()
    {
        $this->getInstance()->setCredentialsFactory(static fn () => new Credentials('keyid', 'SuperKeySecret'));
        $this->assertInstanceOf(CredentialsInterface::class, $this->getInstance()->getCredentials());
    }

    public function test_coris_global_set_credentials_factory_set_the_api_token_value()
    {
        $this->getInstance()->setCredentialsFactory(static fn () => new Credentials('keyid', 'SuperKeySecret'));
        $this->assertSame('SuperKeySecret', $this->getInstance()->getCredentials()->getApiToken());
    }

    public function test_coris_global_set_credentials_factory_set_the_api_key_value()
    {
        $this->getInstance()->setCredentialsFactory(static fn () => new Credentials('keyid', 'SuperKeySecret'));
        $this->assertSame('keyid', $this->getInstance()->getCredentials()->getApiKey());
    }

    public function test_coris_global_set_code_pv()
    {
        $this->getInstance()->codePv('X7924-L9742');
        $this->assertSame('X7924-L9742', $this->getInstance()->codePv());
    }

    private function getInstance()
    {
        return CorisGlobals::getInstance();
    }
}
