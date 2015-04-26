<?php

switch ((string)getenv('ENV')) {
    case '':
    case 'live':
    case 'prod':
    case 'production':
        return include 'production.config.php';

    case 'dev':
    case 'development':
        return include 'development.config.php';

    case 'test':
        return include 'test.config.php';
}

throw new Exception('Invalid ENV given.');