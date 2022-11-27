<?php

namespace Drewlabs\Txn\Coris\Tests;

use Drewlabs\Txn\TransactionPaymentInterface;

class TransactionPaymentStub implements TransactionPaymentInterface
{
    /**
     * 
     * @var string
     */
    private $from;

    /**
     * 
     * @var string
     */
    private $ref;

    /**
     * 
     * @var float|int
     */
    private $value;

    /**
     * 
     * @var string|null
     */
    private $id;

    public function __construct(string $from, string $ref, float $value = 10000, string $id = null)
    {
        $this->from = $from;
        $this->ref = $ref;
        $this->value = $value;
        $this->id = $id;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function getReturnURL()
    {
        return null;
    }

    public function getReference()
    {
        return $this->ref;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getWeight()
    {
        return 'XOF';
    }

    public function createdAt()
    {
        return \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
    }

    public function isProcessed()
    {
        return false;
    }

    public function isPending()
    {
        return true;
    }
}
