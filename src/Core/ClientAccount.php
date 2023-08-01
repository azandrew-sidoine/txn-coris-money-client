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

namespace Drewlabs\Txn\Coris\Core;

class ClientAccount
{
    /**
     * Account number.
     *
     * @var string
     */
    private $number;

    /**
     * Account type.
     *
     * @var string
     */
    private $type;

    /**
     * Creates a client account instance.
     */
    public function __construct(string $type, string $number)
    {
        $this->type = $type;
        $this->number = $number;
    }

    /**
     * Static client account constructor.
     *
     * @return static
     */
    public static function create(\SimpleXMLElement $xml)
    {
        return new static((string) $xml->{'typeCompte'}, (string) $xml->{'numeroCompte'});
    }

    /**
     * Account type getter.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Account number getter.
     *
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }
}
