<?php

namespace Drewlabs\Txn\Coris;

trait HasApiCredentials
{

    /**
     * 
     * @var CredentialsFactory
     */
    private $credentialsFactory;

    /**
     * 
     * @var CredentialsInterface
     */
    private $credentials;

    /**
     * Set the credentials property of this instance
     * 
     * @param EndpointsInterface $endpoints
     * 
     * @return static 
     */
    public function setCredentials(CredentialsInterface $credentials)
    {
        $this->credentials = $credentials;
        return $this;
    }

    /**
     * Getter for the credentials property of this instance
     * 
     * @return CredentialsInterface 
     */
    public function getCredentials()
    {
        if ((null === $this->credentials) &&
            ($this->credentialsFactory instanceof CredentialsFactory)
        ) {
            $this->credentials = ($this->credentialsFactory)();
        }
        return $this->credentials;
    }

    private function getApiToken()
    {
        return $this->getCredentials()
            ->getApiToken();
    }

    private function getApiClient()
    {
        return $this->getCredentials()
            ->getApiKey();
    }


}