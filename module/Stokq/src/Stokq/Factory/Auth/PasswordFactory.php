<?php

namespace Stokq\Factory\Auth;

use Zend\Crypt\Password\Bcrypt;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class PasswordFactory
 * @package Stokq\Factory\Auth
 */
class PasswordFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new Bcrypt();
    }
}