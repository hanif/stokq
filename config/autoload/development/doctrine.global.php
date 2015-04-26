<?php

use Doctrine\DBAL\Driver\PDOMySql\Driver;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Gedmo\Sortable\SortableListener;
use Gedmo\Timestampable\TimestampableListener;

$cfg = include __DIR__ . '/db.local.php';

return [
    'doctrine' => [
        'configuration' => [
            'orm_default' => [
                'metadata_cache'     => 'array',
                'query_cache'        => 'array',
                'result_cache'       => 'array',
                'hydration_cache'    => 'array',
                'driver'             => 'orm_default',
                'generate_proxies'   => true,
                'proxy_dir'          => './data/proxy',
                'proxy_namespace'    => 'DoctrineORMModule\Proxy',
                'filters'            => [],
                'datetime_functions' => [],
                'string_functions'   => [],
                'numeric_functions'  => [],
                'types'              => [],
            ],
        ],
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => './data/migrations',
                'name'      => 'Doctrine ORM Migrations',
                'namespace' => 'DoctrineORMModule\Migrations',
                'table'     => 'orm_migrations',
            ],
        ],
        'eventmanager' => [
            'orm_default' => [
                'subscribers' => [
                    SortableListener::class,
                    TimestampableListener::class,
                ],
            ],
        ],
        'driver' => [
            'annotation' => [
                'class' => AnnotationDriver::class,
                'cache' => 'array',
            ],
        ],
        'connection' => [
            'orm_default' => [
                'driverClass' => Driver::class,
                'params' => [
                    'host'     => $cfg['db']['host'],
                    'port'     => $cfg['db']['port'],
                    'dbname'   => $cfg['db']['dbname'],
                    'user'     => $cfg['db']['username'],
                    'password' => $cfg['db']['password'],
                ],
                'doctrine_type_mappings' => [
                    'enum' => 'string'
                ],
            ],
        ],
    ],
];