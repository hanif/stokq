<?php

namespace Stokq\Factory;

use Stokq\Stdlib\ExceptionHandler;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ExceptionHandlerFactory
 * @package Stokq\Factory
 */
class ExceptionHandlerFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ExceptionHandler();
    }
}