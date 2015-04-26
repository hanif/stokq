<?php

namespace Stokq\Factory\Nav;

use Stokq\Stdlib\Nav;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SidebarNavFactory
 * @package Stokq\Factory\Nav
 */
class SidebarNavFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        if (!isset($config['nav']['sidebar']) || !is_array($config['nav']['sidebar'])) {
            throw new \RuntimeException('The `nav.sidebar` config is not configured properly.');
        }

        return new Nav($config['nav']['sidebar']);
    }
}