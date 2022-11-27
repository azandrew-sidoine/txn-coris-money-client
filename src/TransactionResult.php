<?php

namespace Drewlabs\Txn\Coris;

use Drewlabs\Txn\ProcessTransactionResultInterface;
use Drewlabs\Txn\TransactionPaymentInterface;

class TransactionResult implements ProcessTransactionResultInterface
{

    /**
     * 
     * @var TransactionPaymentInterface
     */
    private $payment;

    /**
     * 
     * @var string|int
     */
    private $code;

    /**
     * 
     * @var string
     */
    private $message;

    /**
     * 
     * @var string|int
     */
    private $pTxnId;

    /**
     * 
     * @var int|string
     */
    private $processedAt;

    /**
     * 
     * @param TransactionPaymentInterface|null $payment 
     * @param mixed $code 
     * @param string $message 
     * @param mixed $pTxnId
     * @param string|null $processedAt
     */
    public function __construct(
        TransactionPaymentInterface $payment = null,
        $code,
        string $message,
        $pTxnId,
        $processedAt = null
    ) {
        $this->payment = $payment;
        $this->code = $code;
        $this->message = $message;
        $this->pTxnId = $pTxnId;
        $this->processedAt = $processedAt;
    }

    public function isValidated()
    {
        return intval($this->code) === 0;
    }

    public function getProcessorReference()
    {
        return $this->pTxnId;
    }

    public function getReference()
    {
        return $this->payment ? $this->payment->getReference() : null;
    }

    public function processedAt()
    {
        return \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', date('Y-m-y H:i:s', isset($this->processedAt) ? strtotime($this->processedAt) : time()));
    }

    public function getStatusText()
    {
        return $this->message;
    }

    public function getResponse()
    {
        return [
            'code' => $this->code,
            'message' => $this->message,
            'transactionId' => $this->pTxnId
        ];
    }
}
