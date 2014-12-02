<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new \Silex\Application();

// Services Configuration
$app->register(
    new \Silex\Provider\TwigServiceProvider(),
    array('twig.path' => __DIR__.'/../views')
);

// Route Definitions
$app->get('/', function () {
    return 'Welcome to the madness';
});

$app->get('/hello/{name}', function($name) use ($app) {
    return $app['twig']->render(
        'hello.html.twig',
        array('name' => $name)
    );
});

return $app;
