<?php

namespace Stokq\View\Helper\Nav;

use Stokq\Stdlib\Nav as NavLib;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

/**
 * Class Nav
 * @package Stokq\View\Helper\Nav
 */
class Nav extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface|AbstractPluginManager
     */
    protected $serviceLocator;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @param string $name
     * @return string
     */
    public function __invoke($name)
    {
        $nav = $this->getServiceManager()->get($name);
        if ($nav instanceof NavLib) {
            return $nav->render();
        }

        return '';
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            $this->serviceLocator = $serviceLocator;
            $this->serviceManager = $serviceLocator->getServiceLocator();
        } else {
            throw new \InvalidArgumentException(sprintf(
                'Arg. passed to %s::setServiceLocator() must be instance of %s.',
                static::class, AbstractPluginManager::class
            ));
        }
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        if (!$this->serviceManager) {
            $this->serviceManager = $this->serviceLocator->getServiceLocator();
        }
        return $this->serviceManager;
    }

}
