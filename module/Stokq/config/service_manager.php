<?php

use Stokq\Factory\Auth\AdapterFactory;
use Stokq\Factory\Auth\PasswordFactory;
use Stokq\Factory\Auth\ServiceFactory;
use Stokq\Factory\Auth\StorageFactory;
use Stokq\Factory\Cache\DoctrineMemcacheFactory;
use Stokq\Factory\Cache\DoctrineRedisFactory;
use Stokq\Factory\Cache\FileCacheFactory;
use Stokq\Factory\Cache\MemcacheFactory;
use Stokq\Factory\Cache\RedisFactory;
use Stokq\Factory\ExceptionHandlerFactory;
use Stokq\Factory\MailTransportFactory;
use Stokq\Factory\MonologFactory;
use Stokq\Factory\Nav\SidebarNavFactory;
use Stokq\Factory\ServiceAbstractFactory;
use Stokq\Factory\SessionManagerFactory;
use Stokq\Factory\ViewMessageFactory;
use Stokq\Stdlib\ExceptionHandler;
use Stokq\Stdlib\ViewMessage;
use Zend\Authentication\Adapter\AdapterInterface as AuthenticationAdapter;
use Zend\Authentication\AuthenticationService;
use Zend\Crypt\Password\PasswordInterface;
use Zend\Mail\Transport\TransportInterface;
use Zend\Session\SessionManager;

return [

    'abstract_factories' => [
        ServiceAbstractFactory::class
    ],

    'aliases' => [
        SessionManager::class           => 'session_manager',
        PasswordInterface::class        => 'auth_password',
        AuthenticationService::class    => 'auth_service',
        AuthenticationAdapter::class    => 'auth_adapter',
        ExceptionHandler::class         => 'exception_handler',
        ViewMessage::class              => 'view_message',
        TransportInterface::class       => 'mail_transport',
    ],

    'factories' => [
        'logger'                        => MonologFactory::class,
        'session_manager'               => SessionManagerFactory::class,
        'sidebar_nav'                   => SidebarNavFactory::class,
        'exception_handler'             => ExceptionHandlerFactory::class,
        'view_message'                  => ViewMessageFactory::class,
        'mail_transport'                => MailTransportFactory::class,

        'file_cache'                    => FileCacheFactory::class,
        'redis'                         => RedisFactory::class,
        'memcache'                      => MemcacheFactory::class,

        'doctrine.cache.redis_cache'    => DoctrineRedisFactory::class,
        'doctrine.cache.memcache_cache' => DoctrineMemcacheFactory::class,

        'auth_password'                 => PasswordFactory::class,
        'auth_storage'                  => StorageFactory::class,
        'auth_service'                  => ServiceFactory::class,
        'auth_adapter'                  => AdapterFactory::class,
    ],

];