<?php

$app = require_once __DIR__ . '/bootstrap.php';

use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

// Services Configuration
$app->register(
    new DoctrineServiceProvider(),
    array(
        'db.options' => array(
            'driver'   => $app['cnf.db.driver'],
            'host'     => $app['cnf.db.host'],
            'dbname'   => $app['cnf.db.dbname'],
            'user'     => $app['cnf.db.user'],
            'password' => $app['cnf.db.password'],
            'charset'  => 'utf8'
        )
    )
);

$app->register(
    new TwigServiceProvider(),
    array('twig.path' => $app['cnf.twig.path'])
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

$app->post('/login', function (Request $request) use ($app) {
    // TODO log in and redirect to /homepage
});

$app->post('/logout', function (Request $request) use ($app) {
    // TODO log out current user and redirect to /
});

$app->post('/homepage', function (Request $request) use ($app) {
    // TODO accept authenticated users only
});

return $app;
