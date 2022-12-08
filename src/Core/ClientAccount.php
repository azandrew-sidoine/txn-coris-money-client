<?php

namespace Drewlabs\Txn\Coris\Core;

use SimpleXMLElement;

class ClientAccount
{
    /**
     * Account number
     * 
     * @var string
     */
    private $number;

    /**
     * Account type
     * 
     * @var string
     */
    private $type;

    /**
     * Creates a client account instance
     * 
     * @param string $type 
     * @param string $number 
     */
    public function __construct(string $type, string $number)
    {
        $this->type = $type;
        $this->number = $number;
    }

    /**
     * Static client account constructor
     * 
     * @param SimpleXMLElement $xml 
     * @return static 
     */
    public static function create(\SimpleXMLElement $xml)
    {
        return new static(
            $xml->{'typeCompte'},
            $xml->{'numeroCompte'},
            $xml->{'prenom'},
            $xml->{'nom'},
            $xml->{'sexe'}
        );
    }

    /**
     * Account type getter
     * 
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Account number getter
     * 
     * @return string 
     */
    public function getNumber()
    {
        return $this->number;
    }
}