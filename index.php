<?php

include "vendor/autoload.php";

$container = require 'config/container.php';
$openAir   = new \OpenAir\OpenAir($container, getenv('receipt'));
$openAir->run();