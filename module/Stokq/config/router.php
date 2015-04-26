<?php

return [
    'routes' => [

        'home' => [
            'type' => 'literal',
            'options' => [
                'route'    => '/',
                'defaults' => [
                    '__NAMESPACE__' => 'Stokq\Controller\Web',
                    'controller' => 'Index',
                    'action' => 'index',
                ],
            ],
        ],

        'web' => [
            'type'    => 'segment',
            'options' => [
                'route'    => '/:controller[/:action]',
                'defaults' => [
                    '__NAMESPACE__' => 'Stokq\Controller\Web',
                    'controller'    => 'index',
                    'action'        => 'index',
                ],
                'constraints' => [
                    'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                ],
            ],
        ],

        'report' => [
            'type'    => 'segment',
            'options' => [
                'route'    => '/reports/:controller[/:action]',
                'defaults' => [
                    '__NAMESPACE__' => 'Stokq\Controller\Web\Reports',
                    'controller'    => 'index',
                    'action'        => 'index',
                ],
                'constraints' => [
                    'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                ],
            ],
        ],

    ],
];