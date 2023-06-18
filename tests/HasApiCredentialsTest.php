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

use Drewlabs\Txn\Coris\Core\Credentials;
use Drewlabs\Txn\Coris\Core\CredentialsInterface;
use Drewlabs\Txn\Coris\HasApiCredentials;
use PHPUnit\Framework\TestCase;

class HasApiCredentialsTest extends TestCase
{
    public function test_coris_global_set_credentials_factory_set_the_credentials_instance()
    {
        $instance = $this->getInstance();
        $instance->setCredentialsFactory(static function() {
            return new Credentials('keyid', 'SuperKeySecret');
        });
        $this->assertInstanceOf(CredentialsInterface::class, $instance->getCredentials());
    }

    public function test_coris_global_set_credentials_factory_set_the_api_token_value()
    {
        $instance = $this->getInstance();
        $instance->setCredentialsFactory(static function() {
            return new Credentials('keyid', 'SuperKeySecret');
        });
        $this->assertSame('SuperKeySecret', $instance->getCredentials()->getApiToken());
    }

    public function test_coris_global_set_credentials_factory_set_the_api_key_value()
    {
        $instance = $this->getInstance();
        $instance->setCredentialsFactory(static function() {
            return new Credentials('keyid', 'SuperKeySecret');
        });
        $this->assertSame('keyid', $instance->getCredentials()->getApiKey());
    }

    private function getInstance()
    {
        return new class()
        {
            use HasApiCredentials;
        };
    }
}
