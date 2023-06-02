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

namespace Drewlabs\Txn\Coris;

use Drewlabs\Txn\ProcessTransactionResultInterface;
use Drewlabs\Txn\TransactionPaymentInterface;

class TransactionResult implements ProcessTransactionResultInterface
{
    /**
     * @var TransactionPaymentInterface
     */
    private $payment;

    /**
     * @var string|int
     */
    private $code;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string|int
     */
    private $pTxnId;

    /**
     * @var int|string
     */
    private $processedAt;

    /**
     * @param mixed       $code
     * @param mixed       $pTxnId
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
        return 0 === (int) $this->code;
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
            'transactionId' => $this->pTxnId,
        ];
    }
}
