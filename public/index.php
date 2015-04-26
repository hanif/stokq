<?php

if (getenv('STATUS') == 'offline') {
    include 'offline.html';
    return;
}

require_once '../vendor/autoload.php';
$config = require_once '../config/application.config.php';

chdir(dirname(__DIR__));
Zend\Mvc\Application::init($config)->run();