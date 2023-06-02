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

namespace Drewlabs\Txn\Coris\Tests;

use Drewlabs\Txn\TransactionalPaymentInterface;
use Drewlabs\Txn\TransactionPaymentInterface;

class TransactionPaymentStub implements TransactionPaymentInterface, TransactionalPaymentInterface
{
    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $ref;

    /**
     * @var float|int
     */
    private $value;

    /**
     * @var string|null
     */
    private $id;

    /**
     * @var string
     */
    private $otp;

    public function __construct(
        string $from,
        string $ref,
        float $value = 10000,
        string $id = null,
        string $otp = null
    ) {
        $this->from = $from;
        $this->ref = $ref;
        $this->value = $value;
        $this->id = $id;
        $this->otp = $otp;
    }

    public function getOTP()
    {
        return $this->otp;
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
