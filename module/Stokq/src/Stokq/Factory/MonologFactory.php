<?php

namespace Stokq\Factory;

use Monolog\Handler\AbstractHandler;
use Monolog\Handler\NullHandler;
use Monolog\Logger;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class MonologFactory
 * @package Stokq\Factory
 */
class MonologFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        $handlers = [NullHandler::class];
        if (isset($config['log']['handlers'])) {
            $handlers = $config['log']['handlers'];
        }

        if (!is_array($handlers)) {
            throw new \RuntimeException('Log handlers config must be an array of class names, factories, or instances.');
        }

        $logger = new Logger('stokq');
        foreach ($handlers as $handler) {
            if (is_string($handler) && class_exists($handler)) {
                $logger->pushHandler(new $handler());
            } else if (is_callable($handler)) {
                $logger->pushHandler($handler($serviceLocator));
            } else if ($handler instanceof AbstractHandler) {
                $logger->pushHandler($handler);
            } else {
                throw new \RuntimeException(
                    'Invalid logger, `log.handlers` config must be an instance of AbstractHandler, class name, of factory'
                );
            }
        }

        return $logger;
    }
}