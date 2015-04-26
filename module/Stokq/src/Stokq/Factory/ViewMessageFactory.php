<?php

namespace Stokq\Factory;

use Stokq\Stdlib\ViewMessage;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ViewMessageFactory
 * @package Stokq\Factory
 */
class ViewMessageFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new ViewMessage();
    }
}