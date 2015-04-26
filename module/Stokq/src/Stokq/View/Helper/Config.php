<?php

namespace Stokq\View\Helper;

use Zend\Mvc\Application;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

/**
 * Class Config
 * @package Stokq\View\Helper
 */
class Config extends AbstractHelper implements ServiceLocatorAwareInterface
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
     * @var array
     */
    protected $applicationConfig = [];

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @param string $name
     * @return mixed|$this
     */
    public function __invoke($name = null)
    {
        if (empty($this->applicationConfig)) {
            $this->applicationConfig = $this->getServiceManager()->get('ApplicationConfig');
        }

        if (empty($this->config)) {
            $this->config = $this->getServiceManager()->get('Config');
        }

        if ($name) {
            return $this->get($name);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @param string $scope
     * @return mixed
     */
    public function get($name, $default = null, $scope = null)
    {
        if (!in_array($scope, ['application', 'app'])) {
            return isset($this->config[$name]) ? $this->config[$name] : $default;
        } else {
            return isset($this->applicationConfig[$name]) ? $this->applicationConfig[$name] : $default;
        }
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return isset($this->applicationConfig['debug']) ? $this->applicationConfig['debug'] : false;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface|AbstractPluginManager $serviceLocator
     * @throws \InvalidArgumentException
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

    /**
     * @return Application
     */
    public function getApp()
    {
        /** @var Application $app */
        return $this->getServiceManager()->get('Application');
    }
}