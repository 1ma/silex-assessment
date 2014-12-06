<?php

$app = require_once __DIR__ . '/bootstrap.php';

use Monolog\Logger;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

// Services Configuration
$app->register(new DoctrineServiceProvider());

$app->register(
    new MonologServiceProvider(),
    array(
        'monolog.logfile' => __DIR__ . '/../var/logs/development.log',
        'monolog.level' => Logger::DEBUG
    )
);

$app->register(new SessionServiceProvider());
$app->register(
    new SecurityServiceProvider(),
    array(
        'security.firewalls' => array(
            'main' => array(
                'pattern' => '^/',
                'anonymous' => true,
                'form' => array('login_path' => '/login', 'login_check' => '/login_check'),
                'logout' => array('logout_path' => '/logout'),
                // TODO Set custom UserProvider
                'users' => array('admin@hospi.dev' => array('ROLE_ADMIN', 'nhDr7OyKlXQju+Ge/WKGrPQ9lPBSUFfpK+B1xqx/+8zLZqRNX0+5G1zBQklXUFy86lCpkAofsExlXiorUcKSNQ=='))
            )
        ),
        'security.access_rules' => array(
            array('^/login$', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/homepage', 'ROLE_ADMIN')
        )
    )
);

$app->register(
    new TwigServiceProvider(),
    array(
        'twig.path' => __DIR__ . '/../views',
        'twig.options' => array('cache' => __DIR__ . '/../var/cache')
    )
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

$app->get('/login', function (Request $request) use ($app) {
    return $app['twig']->render(
        'login.html.twig',
        array(
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        )
    );
});

$app->post('/logout', function (Request $request) use ($app) {
    // TODO log out current user and redirect to /
});

$app->get('/homepage', function (Request $request) use ($app) {
    return "<pre>" . print_r($app['security']->getToken()->getUser(), true) . "</pre>";
});

return $app;
