<?php

use Drewlabs\Txn\Coris\Client;
use Drewlabs\Txn\Coris\Core\ClientAccount;
use Drewlabs\Txn\Coris\Core\ClientInfo;
use Drewlabs\Txn\Coris\Core\CorisGlobals;
use Drewlabs\Txn\Coris\Core\Credentials;
use Drewlabs\Txn\Coris\Tests\TransactionPaymentStub;
use Drewlabs\Txn\Exceptions\RequestException;
use Drewlabs\Txn\ProcessTransactionResultInterface;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{

    private static $configured = false;

    public function runUnitTests(\Closure $test)
    {
        if (self::$configured !== true) {
            CorisGlobals::getInstance()->setCredentialsFactory(function() {
                return new Credentials('Test', 'Test');
            });
        }
        ($test)();
    }


    public function test_client_constructor_runs_without_error()
    {
        $this->runUnitTests(function() {
            $client = new Client('http://localhost:8888');
            $this->assertInstanceOf(Client::class, $client);
        });
    }

    public function test_client_to_process_transaction_result()
    {
        $this->runUnitTests(function() {
            $client = new Client('http://localhost:8888');
            $result = $client->toProcessTransactionResult(['code' => -1, 'transactionId' => null]);
    
            $this->assertInstanceOf(ProcessTransactionResultInterface::class, $result);
            $this->assertEquals(false, $result->isValidated());
            $this->assertEquals(null, $result->getProcessorReference());
            $this->assertEquals('Unknown Error!', $result->getStatusText());
        });
    }

    public function test_client_create_hash_string()
    {
        $this->runUnitTests(function() {
            $client = new Client('https://testbed.corismoney.com');
            $hash = $client->createHashString(sprintf("%s%s%s", '228', '91969456', $client->getApiToken()));
            $this->assertTrue(is_string($hash));
            $this->assertEquals($client->computeHash(sprintf("%s%s%s", '228', '91969456', $client->getApiToken())), $hash);
        });
    }

    public function test_client_request_otp_throws_unexpected_value_exception_if_payeer_id_does_not_conform_required_format()
    {
        $this->runUnitTests(function() {
            $this->expectException(UnexpectedValueException::class);
            $client = new Client('https://testbed.corismoney.com');
            $client->requestOTP('22661347475');
        });
    }

    // public function test_coris_client_request_otp()
    // {
    //     $this->runUnitTests(function() {
    //         $client = new Client('https://testbed.corismoney.com');
    //         $result = $client->requestOTP('228 92146591');
    //         $this->assertTrue(is_bool($result));
    //     });
    // }

    public function test_coris_client_request_client_infos()
    {
        $this->expectException(RequestException::class);
        $this->runUnitTests(function() {
            $client = new Client('https://testbed.corismoney.com');
            $result = $client->getClientInfo('228', '92146591');
            $this->assertTrue($result instanceof ClientInfo);
            $this->assertTrue($result->account() instanceof ClientAccount);
        });
    }

    public function test_coris_client_request_process_txn_payment()
    {
        $this->expectException(RequestException::class);
        $this->runUnitTests(function() {
            $times = 0;
            $result = null;
            $client = new Client('https://testbed.corismoney.com');
            $client->addTransactionResponseLister(function($value) use (&$times, &$result) {
                $result = $value;
                $times += 1;
            });
            $result = $client->processTransaction(
                new TransactionPaymentStub(
                    '228 92146591',
                    $this->guidv4(),
                    100,
                    $this->guidv4(),
                    '47949'
                )
            );
            $this->assertEquals(1, $times, 'Expect the listener callback to be called at least once if request was successful');
            $this->assertTrue(null !== $result);
            $this->assertTrue(is_bool($result));
        });
    }

    public function guidv4($data = null)
    {
        // Generate 16 bytes (128 bits) of random data or use the data passed into the function.
        $data = $data ?? random_bytes(16);
        assert(strlen($data) == 16);

        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        // Output the 36 character UUID.
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
