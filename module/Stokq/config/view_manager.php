<?php

return [
    'doctype'                  => 'HTML5',
    'not_found_template'       => 'error/not-found',
    'exception_template'       => 'error/exception',
    'template_map' => [
        'layout/layout'         => __DIR__ . '/../view/layout/default.phtml',
        'layout/blank'          => __DIR__ . '/../view/layout/blank.phtml',
        'layout/default'        => __DIR__ . '/../view/layout/default.phtml',
        'layout/single-column'  => __DIR__ . '/../view/layout/single-column.phtml',
        'layout/list-detail'    => __DIR__ . '/../view/layout/list-detail.phtml',
        'error/not-found'       => __DIR__ . '/../view/error/not-found.phtml',
        'error/exception'       => __DIR__ . '/../view/error/exception.phtml',
        'error/index'           => __DIR__ . '/../view/error/exception.phtml',
    ],
    'template_path_stack' => [
        __DIR__ . '/../view',
    ],
];