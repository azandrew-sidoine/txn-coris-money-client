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


use Drewlabs\Libman\Contracts\RepositoryInterface;

trait ConfigRespositoryAware
{
    private $configRepository;

    /**
     * set the configuration repository for the current instance
     * 
     * @param RepositoryInterface $repository 
     * 
     * @return static 
     */
    public function setConfigRepository(RepositoryInterface $repository)
    {
        $this->configRepository = $repository;
        return $this;
    }

    /**
     * returns the configuration repository for the current instance
     * 
     * @return RepositoryInterface 
     */
    public function getConfigRepository()
    {
        return $this->configRepository;
    }

    /**
     * resolve value for the `$name` key from the repository
     * 
     * @param string $name 
     * @param mixed $default 
     * @return mixed 
     */
    public function getConfig(string $name, $default = null)
    {
        $default = !is_string($default) && is_callable($default) ? $default : function() use ($default) {
            return $default;
        };
        if (null === $this->configRepository) {
            return $default();
        }

        // Use the configRepository to resolve value for the `$name` variable
        $result = $this->configRepository->get($name);

        // fallback to default value is `$result` is null
        return null !== $result ? $result : $default();
    }
}
