<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();

// Services Configuration
$app->register(
    new DoctrineServiceProvider(),
    array(
        'db.options' => array(
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'dbname' => 'hospi',
            'user' => 'root',
            'password' => 'root',
            'charset' => 'utf8'
        )
    )
);

$app->register(
    new TwigServiceProvider(),
    array('twig.path' => __DIR__ . '/../views')
);

// Route Definitions
$app->get('/', function () use ($app) {
    return 'Welcome to the madness';
});

$app->get('/hello/{name}', function ($name) use ($app) {
    return $app['twig']->render(
        'hello.html.twig',
        array('name' => $name)
    );
});

$app->get('/insert', function (Request $request) use ($app) {
    $email = $request->query->get('email');
    $password = $request->query->get('password');

    if (null === $email) {
        return 'email parameter is missing';
    }

    if (null === $password) {
        return 'password parameter is missing';
    }

    $app['db']->insert('users', array('email' => $email, 'password' => $password));

    return 'Successful insert';
});

$app['debug'] = true;

return $app;
