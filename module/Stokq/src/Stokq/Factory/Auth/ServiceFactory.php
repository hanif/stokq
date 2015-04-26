<?php

namespace Stokq\Factory\Auth;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Storage\StorageInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ServiceFactory
 * @package Stokq\Factory\Auth
 */
class ServiceFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $storage = $serviceLocator->get('auth_storage');

        if ($storage instanceof StorageInterface) {
            $service = new AuthenticationService();
            $service->setStorage($storage);
            return $service;
        }

        throw new \RuntimeException('Could not get StorageInterface service instance.');
    }
}