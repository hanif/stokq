<?php

namespace Stokq\Factory\Auth;

use Stokq\Authentication\Adapter\DoctrineORM;
use Zend\Console\Request as ConsoleRequest;
use Zend\Http\PhpEnvironment\Request as HttpRequest;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AdapterFactory
 * @package Stokq\Factory\Auth
 */
class AdapterFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $authConfig = $config['authentication']['doctrine_orm'];
        $request = $serviceLocator->get('Request');

        $adapter = new DoctrineORM($serviceLocator);
        if ($request instanceof HttpRequest) {
            $adapter->setIdentity($request->getPost($authConfig['identity_input']));
            $adapter->setCredentials($request->getPost($authConfig['credentials_input']));
        } else if ($request instanceof ConsoleRequest) {
            $adapter->setIdentity($request->getParam($authConfig['identity_input']));
            $adapter->setCredentials($request->getParam($authConfig['credentials_input']));
        } else {
            throw new \DomainException(
                sprintf('Request object is not an instance of `%s` nor `%s`.', HttpRequest::class, ConsoleRequest::class)
            );
        }

        return $adapter;
    }
}