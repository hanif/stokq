<?php

namespace Stokq\Factory\Cache;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class RedisFactory
 * @package Stokq\Factory\Cache
 */
class RedisFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $redis = new \Redis();
        $redis->connect('localhost', 6379);
        return $redis;
    }
}