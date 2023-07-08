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

use Drewlabs\Txn\Coris\Core\Credentials;
use Drewlabs\Txn\Coris\Core\CredentialsInterface as CoreCredentialsInterface;
use PHPUnit\Framework\TestCase;

class CredentialsTest extends TestCase
{
    public function test_credentials_constructor_executes_without_error()
    {
        $credentials = new Credentials('keyid', 'SuperSecret');
        $this->assertInstanceOf(CoreCredentialsInterface::class, $credentials);
    }

    public function test_credentials_get_api_key()
    {
        $credentials = new Credentials('keyid', 'SuperSecret');
        $this->assertSame('keyid', $credentials->getApiKey());
    }

    public function test_credentials_get_api_token()
    {
        $credentials = new Credentials('keyid', 'SuperSecret');
        $this->assertSame('SuperSecret', $credentials->getApiToken());
    }
}
