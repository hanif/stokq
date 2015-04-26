<?php

namespace Stokq\Factory;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Stokq\Service\ServiceInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Stdlib\InitializableInterface;

/**
 * Class ServiceAbstractFactory
 * @package Stokq\Factory
 */
class ServiceAbstractFactory implements AbstractFactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param $cName
     * @param $rName
     * @return bool
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $cName, $rName)
    {
        if (class_exists($rName) && in_array(ServiceInterface::class, class_implements($rName))) {
            return true;
        }

        return false;
    }

    /**
     * @param ServiceLocatorInterface|ServiceManager $serviceLocator
     * @param $cName
     * @param $rName
     * @return mixed
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $cName, $rName)
    {
        $service = new $rName();

        if ($service instanceof ServiceLocatorAwareInterface) {
            $service->setServiceLocator($serviceLocator);
        }

        if ($service instanceof ServiceManagerAwareInterface) {
            $service->setServiceManager($serviceLocator);
        }

        if ($service instanceof LoggerAwareInterface) {
            /** @var LoggerInterface $logger */
            $logger = $serviceLocator->get('logger');
            $service->setLogger($logger);
        }

        if ($service instanceof EventManagerAwareInterface) {
            $service->setEventManager(new EventManager(get_class($service)));
        }

        if ($service instanceof InitializableInterface) {
            $service->init();
        }

        return $service;
    }
}