<?php

$app = require_once __DIR__ . '/app.php';

$app['debug'] = true;

$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'host'     => 'localhost',
    'dbname'   => 'hospi',
    'user'     => 'root',
    'password' => 'root',
    'charset'  => 'utf8'
);

$app['monolog.level'] = \Monolog\Logger::DEBUG;

return $app;
