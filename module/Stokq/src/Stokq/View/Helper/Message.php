<?php

namespace Stokq\View\Helper;

use Stokq\Stdlib\ViewMessage;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

/**
 * Class Message
 * @package Stokq\View\Helper
 */
class Message extends AbstractHelper implements ServiceLocatorAwareInterface
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
     * @var ViewMessage
     */
    protected $viewMessage;

    /**
     * @return string
     */
    public function __toString()
    {
        if (!$this->getViewMessage()->count()) {
            return '';
        }

        $markup = [];
        foreach (['danger', 'warning', 'success', 'info', ['ok' => 'success'], ['error' => 'danger']] as $type) {
            $markup[] = $this->render($type);
        }

        return join("\n", $markup);
    }

    /**
     * @param string|null $type
     * @return string
     */
    public function __invoke($type = null)
    {
        if (null === $type) {
            return $this;
        } else {
            return $this->render($type);
        }
    }

    /**
     * @param string $type
     * @return string
     */
    public function render($type)
    {
        $class = $type;
        if (is_array($type)) {
            list ($type, $class) = each($type);
        }

        $messages = $this->getViewMessage()->getMessages();
        $dismiss = '<button type="button" class="close" data-dismiss="alert" data-aria-hidden="true">&times;</button>';

        if (isset($messages[$type])) {
            if (count($messages[$type]) > 1) {
                $text = sprintf('<ul><li>%s</li></ul>', join('</li><li>', $messages[$type]));
                return sprintf('<div class="alert alert-%s">%s%s</div>', $class, $dismiss, $text);
            } else {
                $message = $messages[$type][0];
                return sprintf('<div class="alert alert-%s">%s<span>%s</span></div>', $class, $dismiss, $message);
            }
        }

        return '';
    }

    /**
     * @return string
     */
    public function danger()
    {
        return $this->render('danger');
    }

    /**
     * @return string
     */
    public function error()
    {
        return $this->render('error');
    }

    /**
     * @return string
     */
    public function warning()
    {
        return $this->render('warning');
    }

    /**
     * @return string
     */
    public function success()
    {
        return $this->render('success');
    }

    /**
     * @return string
     */
    public function info()
    {
        return $this->render('info');
    }

    /**
     * @return ViewMessage
     */
    public function getViewMessage()
    {
        if (!$this->viewMessage instanceof ViewMessage) {
            $this->viewMessage = $this->serviceManager->get(ViewMessage::class);
        }
        return $this->viewMessage;
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
}
