<?php

namespace Stokq\Factory\Cache;

use Zend\Cache\Storage\Adapter\Filesystem;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class FileCacheFactory
 * @package Stokq\Factory\Cache
 */
class FileCacheFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['cache']['file']['path'])) {
            throw new \RuntimeException('Unable to initialize file cache service, the `cache.file.path` config is not set.');
        }

        $cachePath = $config['cache']['file']['path'];
        if (!is_dir($cachePath) || !is_writable($cachePath)) {
            throw new \RuntimeException('Cache dir is not valid or not writable.');
        }

        if (!is_dir($cachePath)) {
            mkdir($cachePath);
        }

        if (!is_dir($cachePath)) {
            throw new \RuntimeException(sprintf('Directory `%s` does not exists.', $cachePath));
        }

        if (!is_writable($cachePath)) {
            throw new \RuntimeException(sprintf('Directory `%s` is not writable.', $cachePath));
        }

        $cache = new Filesystem();
        $cache->getOptions()->setCacheDir($cachePath);
        return $cache;
    }
}