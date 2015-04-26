<?php

return [
    'router'             => include 'router.php',
    'authentication'     => include 'authentication.php',
    'service_manager'    => include 'service_manager.php',
    'controllers'        => include 'controllers.php',
    'controller_plugins' => include 'controller_plugins.php',
    'view_manager'       => include 'view_manager.php',
    'view_helpers'       => include 'view_helpers.php',
    'doctrine'           => include 'doctrine.php',
    'upload'             => include 'upload.php',
    'nav'                => [
                                'sidebar' => include 'nav.sidebar.php',
                            ],
];