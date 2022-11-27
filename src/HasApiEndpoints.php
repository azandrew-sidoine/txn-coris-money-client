<?php

namespace Drewlabs\Txn\Coris;

trait HasApiEndpoints
{

    /**
     * 
     * @var EndpointsInterface|null
     */
    private $endpoints;

    /**
     * Set the endpoints property of this instance
     * 
     * @param EndpointsInterface $endpoints
     * 
     * @return static 
     */
    public function setEndpoints(EndpointsInterface $endpoints)
    {
        $this->endpoints = $endpoints;
        return $this;
    }

    /**
     * Getter for the endpoints property of this instance
     * 
     * @return EndpointsInterface|null 
     */
    public function getEndpoints()
    {
        if (null === $this->endpoints) {
            $this->useDefaultEndpoints();
        }
        return $this->endpoints;
    }

    /**
     * Set the client ot use the default endpoints
     * 
     * @return void 
     */
    public function useDefaultEndpoints()
    {
        $this->endpoints = Endpoints::defaults();
    }

}