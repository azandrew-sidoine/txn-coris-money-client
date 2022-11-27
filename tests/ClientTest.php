<?php

use Drewlabs\Txn\Coris\Client;
use Drewlabs\Txn\Coris\Credentials;
use Drewlabs\Txn\Coris\Tests\TransactionPaymentStub;
use Drewlabs\Txn\ProcessTransactionResultInterface;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{

    private function createClientCredentials()
    {
        return new Credentials('CNSS', '$2a$10$JpGsCNuqTznfONRCNRPZCeVjVkztgMoE32RHoCvAabImznwPN2NXS');
    }

    public function test_client_constructor_runs_without_error()
    {
        $client = new Client('http://localhost:8888');
        $this->assertInstanceOf(Client::class, $client);
    }

    public function test_client_constructor_throws_exception_if_second_argument_does_not_match_supported_types()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Client('http://localhost:8888', new \stdClass);
    }

    public function test_client_to_process_transaction_result()
    {
        $client = new Client('http://localhost:8888');
        $result = $client->toProcessTransactionResult(['code' => -1, 'transactionId' => null]);

        $this->assertInstanceOf(ProcessTransactionResultInterface::class, $result);
        $this->assertEquals(false, $result->isValidated());
        $this->assertEquals(null, $result->getProcessorReference());
        $this->assertEquals('Unknown Error!', $result->getStatusText());
    }

    public function test_client_create_hash_string()
    {
        $client = new Client('https://testbed.corismoney.com', $this->createClientCredentials());
        $hash = $client->createHashString(sprintf("%s%s%s", '228', '91969456', $client->getApiToken()));
        $this->assertTrue(is_string($hash));
        $this->assertEquals($client->computeHash(sprintf("%s%s%s", '228', '91969456', $client->getApiToken())), $hash);
    }

    public function test_client_request_otp_throws_unexpected_value_exception_if_payeer_id_does_not_conform_required_format()
    {
        $this->expectException(UnexpectedValueException::class);
        $client = new Client('https://testbed.corismoney.com', $this->createClientCredentials());
        $client->requestOTP('22891969456');
    }

    public function test_client_request_otp()
    {
        $client = new Client('https://testbed.corismoney.com', $this->createClientCredentials());
        $result = $client->requestOTP('228 92146591');
        $this->assertTrue(is_bool($result));
    }

    public function test_coris_client_request_client_infos()
    {
        $client = new Client('https://testbed.corismoney.com', $this->createClientCredentials());
        $result = $client->getClientInfo('228', '92146591');
        $this->assertTrue(is_object($result));
    }

    public function test_coris_client_request_process_txn_payment()
    {
        $client = new Client('https://testbed.corismoney.com', $this->createClientCredentials());
        $result = $client->processTransaction(new TransactionPaymentStub('228 92146591', $this->guidv4(), 50000, $this->guidv4()));
        $this->assertTrue(is_bool($result));
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