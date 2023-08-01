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

use Drewlabs\Libman\LibraryConfig;
use Drewlabs\Txn\Coris\Client;
use Drewlabs\Txn\Coris\Core\ClientAccount;
use Drewlabs\Txn\Coris\Core\ClientInfo;
use Drewlabs\Txn\Coris\Factory;
use Drewlabs\Txn\Exceptions\RequestException;
use Drewlabs\Txn\ProcessTransactionResultInterface;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function runUnitTests(Closure $test)
    {
        $client = Factory::createInstance(LibraryConfig::create([
            'name' => 'drewlabs/coris-txn-client',
            'type' => 'composer',
            'auth' => [
                'id' => 'coris',
                'secret' => 'CorisSecret',
            ],
            'configuration' => [
                'api' => [
                    'host' => 'http://127.0.0.1',
                    'credentials' => [
                        'name' => 'coris',
                        'token' => 'CorisSecret',
                    ],
                    'paths' => [
                        'base' => 'base/path',
                    ],
                ],
                'sale_point' => '8035542',
            ],
        ]));
        ($test)($client);
    }

    public function test_client_constructor_runs_without_error()
    {
        $this->runUnitTests(function ($client) {
            $this->assertInstanceOf(Client::class, $client);
        });
    }

    public function test_client_to_process_transaction_result()
    {
        $this->runUnitTests(function ($client) {
            $result = $client->toProcessTransactionResult(['code' => -1, 'transactionId' => null]);

            $this->assertInstanceOf(ProcessTransactionResultInterface::class, $result);
            $this->assertFalse($result->isValidated());
            $this->assertNull($result->getProcessorReference());
            $this->assertSame('Unknown Error!', $result->getStatusText());
        });
    }

    public function test_client_create_hash_string()
    {
        $this->runUnitTests(function (Client $client) {
            $hash = $client->createHashString(sprintf('%s%s%s', '228', '91969456', $client->getApiToken()));
            $this->assertTrue(is_string($hash));
            $this->assertSame($client->computeHash(sprintf('%s%s%s', '228', '91969456', $client->getApiToken())), $hash);
        });
    }

    public function test_client_request_otp_throws_unexpected_value_exception_if_payeer_id_does_not_conform_required_format()
    {
        $this->runUnitTests(function ($client) {
            $this->expectException(UnexpectedValueException::class);
            $client->requestOTP('22661347475');
        });
    }

    public function test_coris_client_request_client_infos()
    {
        $this->expectException(RequestException::class);
        $this->runUnitTests(function ($client) {
            $result = $client->getClientInfo('228', '92146591');
            $this->assertInstanceOf(ClientInfo::class, $result);
            $this->assertTrue($result->account() instanceof ClientAccount);
        });
    }

    // public function test_coris_client_request_process_txn_payment()
    // {
    //     $this->expectException(RequestException::class);
    //     $this->runUnitTests(function ($client) {
    //         $times = 0;
    //         $result = null;
    //         $client->addTransactionResponseLister(static function ($value) use (&$times, &$result) {
    //             $result = $value;
    //             ++$times;
    //         });
    //         $result = $client->processTransaction(
    //             new TransactionPaymentStub(
    //                 '228 92146591',
    //                 $this->guidv4(),
    //                 100,
    //                 $this->guidv4(),
    //                 '47949'
    //             )
    //         );
    //         $this->assertSame(1, $times, 'Expect the listener callback to be called at least once if request was successful');
    //         $this->assertTrue(null !== $result);
    //         $this->assertTrue(is_bool($result));
    //     });
    // }

    public function guidv4($data = null)
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = null !== $data ? $data : random_bytes(16);
        assert(16 === strlen($data));

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0F | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3F | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
