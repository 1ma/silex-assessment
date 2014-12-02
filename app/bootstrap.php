<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new \Silex\Application();

$app['debug'] = true;

$app['cnf.twig.path'] = __DIR__ . '/../views';

$app['cnf.db.driver']   = 'pdo_mysql';
$app['cnf.db.host']     = 'localhost';
$app['cnf.db.dbname']   = 'hospi';
$app['cnf.db.user']     = 'root';
$app['cnf.db.password'] = 'root';

return $app;
