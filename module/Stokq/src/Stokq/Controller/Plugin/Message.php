<?php

namespace Stokq\Controller\Plugin;

use Stokq\Stdlib\ViewMessage;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class Message
 * @package Stokq\Controller\Plugin
 */
class Message extends AbstractPlugin implements ServiceLocatorAwareInterface
{
    /**
     * @var ViewMessage
     */
    protected $message;

    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @param string $type
     * @param string $message
     * @return $this
     */
    public function __invoke($type = null, $message = null)
    {
        if ($type && $message) {
            $this->getMessage()->add($type, $message);
        }

        return $this;
    }

    /**
     * @return ViewMessage
     */
    public function getMessage()
    {
        if (!$this->message) {
            /** @var ServiceLocatorAwareInterface $locator */
            $locator = $this->getServiceLocator();
            $this->message = $locator->getServiceLocator()->get(ViewMessage::class);
        }

        return $this->message;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function reset($type)
    {
        $this->getMessage()->clear($type);
        return $this;
    }

    /**
     * @param string $message
     * @return $this
     */
    public function success($message)
    {
        return $this('success', $message);
    }

    /**
     * @param string $message
     * @return $this
     */
    public function info($message)
    {
        return $this('info', $message);
    }

    /**
     * @param string $message
     * @return $this
     */
    public function warning($message)
    {
        return $this('warning', $message);
    }

    /**
     * @param string $message
     * @return $this
     */
    public function danger($message)
    {
        return $this('danger', $message);
    }

    /**
     * @param string $message
     * @return $this
     */
    public function error($message)
    {
        return $this('danger', $message);
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
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
}