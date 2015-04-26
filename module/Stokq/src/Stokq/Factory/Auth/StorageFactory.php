<?php

namespace Stokq\Factory\Auth;

use Zend\Authentication\Storage\Session;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Session\SessionManager;

/**
 * Class StorageFactory
 * @package Stokq\Factory\Auth
 */
class StorageFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $manager = $serviceLocator->get('session_manager');
        if ($manager instanceof SessionManager) {
            $config = $serviceLocator->get('ApplicationConfig');
            $namespace = sprintf('usr_%s', $config['tenant_id']);
            $storage = new Session($namespace, null, $manager);
            return $storage;
        }

        throw new \RuntimeException('Could not get SessionManager service instance.');
    }
}