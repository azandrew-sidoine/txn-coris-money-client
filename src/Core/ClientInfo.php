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

class ClientInfo
{
    /**
     * Client agency id.
     *
     * @var string|int
     */
    private $agence;

    /**
     * Client firstname.
     *
     * @var string
     */
    private $firstname;

    /**
     * Client lastname.
     *
     * @var string
     */
    private $lastname;

    /**
     * Client sex or gender.
     *
     * @var string
     */
    private $sex;

    /**
     * Client identity document name.
     *
     * @var string
     */
    private $iddoc;

    /**
     * Client reference.
     *
     * @var string
     */
    private $reference;

    /**
     * Client type.
     *
     * @var string
     */
    private $type;

    /**
     * @var ClientAccount
     */
    private $account;

    /**
     * Creates a client info instance.
     *
     * @param mixed $agence
     */
    public function __construct(
        $agence,
        string $type,
        string $firstname,
        string $lastname,
        string $sex = null,
        ClientAccount $account = null,
        string $ref = null
    ) {
        $this->agence = $agence;
        $this->type = $type;
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->sex = $sex ?? $this->sex;
        $this->account = $account ?? $this->account;
        $this->reference = $ref;
    }

    /**
     * Creates a class intance from Simple xml object.
     *
     * @return static
     */
    public static function create(\SimpleXMLElement $xml)
    {
        $object = new static(
            $xml->{'codeAgence'}->__toString(),
            $xml->{'typeClient'},
            $xml->{'prenom'},
            $xml->{'nom'},
            $xml->{'sexe'}
        );
        $object->iddoc($xml->{'piece'});
        // TODO : Check more that one account possibilities
        $object->account(ClientAccount::create($xml->{'listCompte'}));
        $object->ref($xml->{'reference'});

        return $object;
    }

    /**
     * Agence getter and setter.
     *
     * @param mixed $value
     *
     * @return string|int
     */
    public function agence($value = null)
    {
        if (null !== $value) {
            $this->agence = (string) $value;
        }

        return $this->agence;
    }

    /**
     * Firstname getter and setter.
     *
     * @return string
     */
    public function firstname(string $value = null)
    {
        if (null !== $value) {
            $this->firstname = (string) $value;
        }

        return $this->firstname;
    }

    /**
     * Lastname getter and setter.
     *
     * @return string
     */
    public function lastname(string $value = null)
    {
        if (null !== $value) {
            $this->lastname = (string) $value;
        }

        return $this->lastname;
    }

    /**
     * Sex getter and setter.
     *
     * @return string
     */
    public function sex(string $value = null)
    {
        if (null !== $value) {
            $this->sex = (string) $value;
        }

        return $this->sex;
    }

    /**
     * Identity document getter and setter.
     *
     * @return string
     */
    public function iddoc(string $value = null)
    {
        if (null !== $value) {
            $this->iddoc = (string) $value;
        }

        return $this->iddoc;
    }

    /**
     * Client reference getter and setter.
     *
     * @return string
     */
    public function ref(string $value = null)
    {
        if (null !== $value) {
            $this->reference = (string) $value;
        }

        return $this->reference;
    }

    /**
     * Client type getter and setter.
     *
     * @return string
     */
    public function type(string $value = null)
    {
        if (null !== $value) {
            $this->type = (string) $value;
        }

        return $this->type;
    }

    /**
     * Client account object getter and setter.
     *
     * @return string
     */
    public function account(ClientAccount $value = null)
    {
        if (null !== $value) {
            $this->account = $value;
        }

        return $this->account;
    }
}
