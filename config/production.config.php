<?php

return [

    'debug' => false,
    'tenant_id' => 'local',
    'app_mailer' => 'notification@stokq.com',

    'modules' => [
        'ZendSentry',
        'Stokq',
        'DoctrineModule',
        'DoctrineORMModule',
        'SlmMail',
    ],

    'module_listener_options' => [
        'module_paths' => [
            './module',
            './vendor',
        ],

        'config_glob_paths' => [
            sprintf('%s/autoload/production/{,*.}{global,local}.php', __DIR__),
        ],

        'config_cache_enabled'      => true,
        'module_map_cache_enabled'  => true,
        'check_dependencies'        => false,
        'config_cache_key'          => 'cfg_cache',
        'module_map_cache_key'      => 'mod_map_cache',
        'cache_dir'                 => './data/cache',
    ],

];