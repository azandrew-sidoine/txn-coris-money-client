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

trait HasApiEndpoints
{
    /**
     * @var EndpointsInterface|null
     */
    private $endpoints;

    /**
     * Set the endpoints property of this instance.
     *
     * @return static
     */
    public function setEndpoints(EndpointsInterface $endpoints)
    {
        $this->endpoints = $endpoints;

        return $this;
    }

    /**
     * Getter for the endpoints property of this instance.
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
     * Set the client ot use the default endpoints.
     *
     * @return void
     */
    public function useDefaultEndpoints()
    {
        $this->endpoints = Endpoints::defaults();
    }
}
