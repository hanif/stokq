<?php

return [

    'debug' => true,
    'tenant_id' => 'local',
    'app_mailer' => 'notification@stokq.com',

    'modules' => [
        'Stokq',
        'DoctrineModule',
        'DoctrineORMModule',
        'ZendDeveloperTools',
    ],

    'module_listener_options' => [
        'module_paths' => [
            './module',
            './vendor',
        ],

        'config_glob_paths' => [
            sprintf('%s/autoload/development/{,*.}{global,local}.php', __DIR__),
        ],

        'config_cache_enabled' => false,
        'module_map_cache_enabled' => false,
    ],

];