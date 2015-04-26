<?php

namespace Stokq\Factory\Cache;

use Doctrine\Common\Cache\RedisCache;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DoctrineRedisFactory
 * @package Stokq\Factory\Cache
 */
class DoctrineRedisFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $redis = $serviceLocator->get('redis');

        $config = $serviceLocator->get('ApplicationConfig');
        if (is_array($config) && isset($config['tenant_id'])) {
            if ($redis instanceof \Redis) {
                $cache = new RedisCache();
                $cache->setNamespace(sprintf('stokq.doctrine.%s', $config['tenant_id']));
                $cache->setRedis($redis);
                return $cache;
            }
        } else {
            throw new \RuntimeException("Unable to get `tenant_id` from config.");
        }

        throw new \RuntimeException("Invalid redis instance returned.");
    }
}