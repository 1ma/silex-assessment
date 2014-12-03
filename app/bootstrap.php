<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new \Silex\Application();

$app['debug'] = true;

$app['db.options'] = array(
    'driver'   => 'pdo_mysql',
    'host'     => 'localhost',
    'dbname'   => 'hospi',
    'user'     => 'root',
    'password' => 'root',
    'charset'  => 'utf8'
);

return $app;
