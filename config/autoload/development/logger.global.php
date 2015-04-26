<?php

use Monolog\Handler\ChromePHPHandler;
use Monolog\Handler\FirePHPHandler;

return [
    'log' => [
        'handlers' => [
            ChromePHPHandler::class,
            FirePHPHandler::class
        ]
    ]
];