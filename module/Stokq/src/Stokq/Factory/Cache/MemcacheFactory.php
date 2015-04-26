<?php

namespace Stokq\Factory\Cache;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class MemcacheFactory
 * @package Stokq\Factory\Cache
 */
class MemcacheFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $memcache = new \Memcache();
        $memcache->connect('localhost', 11211);
        return $memcache;
    }
}