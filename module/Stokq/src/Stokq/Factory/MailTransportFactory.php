<?php

namespace Stokq\Factory;

use Zend\Mail\Transport\Sendmail;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class MailTransportFactory
 * @package Stokq\Factory
 */
class MailTransportFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('ApplicationConfig');

        if (isset($config['debug']) && !$config['debug']) {
            try {
                return $serviceLocator->get('SlmMail\Mail\Transport\MailgunTransport');
            } catch (ServiceNotFoundException $e) {}
        }

        return new Sendmail();
    }
}