<?php

include "vendor/autoload.php";

$fp = @fsockopen('webdriver', 4444, $errno, $errstr, 10);

if ($errno) {
    sleep(10);
}

$container = require 'config/container.php';
$app = new \App\App($container, getenv('receipt'));
$app->run();
