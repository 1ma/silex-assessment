<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new \Silex\Application();

$app->get('/', function () {
    return 'Welcome to the madness';
});

return $app;
