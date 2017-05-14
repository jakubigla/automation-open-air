<?php

use Zend\ServiceManager\Config;
use Zend\ServiceManager\ServiceManager;

// Load configuration
$config = require __DIR__ . '/dependencies.php';

// Build container
$container = new ServiceManager();
(new Config($config))->configureServiceManager($container);

return $container;
