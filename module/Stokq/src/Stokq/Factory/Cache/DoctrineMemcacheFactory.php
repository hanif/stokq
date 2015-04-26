<?php

namespace Stokq\Factory\Cache;

use Doctrine\Common\Cache\MemcacheCache;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DoctrineMemcacheFactory
 * @package Stokq\Factory\Cache
 */
class DoctrineMemcacheFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $memcache = $serviceLocator->get('memcache');

        $config = $serviceLocator->get('ApplicationConfig');
        if (is_array($config) && isset($config['tenant_id'])) {
            if ($memcache instanceof \Memcache) {
                $cache = new MemcacheCache();
                $cache->setNamespace(sprintf('stokq.doctrine.%s', $config['tenant_id']));
                $cache->setMemcache($memcache);
                return $cache;
            }
        } else {
            throw new \RuntimeException("Unable to get `tenant_id` from config.");
        }

        throw new \RuntimeException("Invalid memcache instance returned.");
    }
}