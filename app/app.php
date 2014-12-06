<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Hospi\Model\UserProvider;
use Silex\Application;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;

$app = new Application();

// Services Configuration
$app->register(new DoctrineServiceProvider());

$app->register(
    new MonologServiceProvider(),
    array('monolog.logfile' => __DIR__ . '/../var/logs/development.log')
);

$app->register(new SessionServiceProvider());
$app->register(
    new SecurityServiceProvider(),
    array(
        'security.firewalls' => array(
            // Permet usuaris anonims a totes les rutes i s'identifiquen per formulari
            'main' => array(
                'pattern' => '^/',
                'anonymous' => true,
                'form' => array('login_path' => '/login', 'login_check' => '/login_check'),
                'logout' => array('logout_path' => '/logout'),
                'users' => $app->share(function () use ($app) {
                    return new UserProvider($app['db']);
                })
            )
        ),
        'security.access_rules' => array(
            // Deixa entrar al login, arrel i formulari de registre als usuaris anonims
            array('^/$', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/login$', 'IS_AUTHENTICATED_ANONYMOUSLY'),
            array('^/register', 'IS_AUTHENTICATED_ANONYMOUSLY'),

            // A qualsevol altre ruta nomes poden entrar els usuaris identificats i amb rol ROLE_USER
            array('^/.*$', 'ROLE_USER')
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
    $text = 'Welcome to the madness bro<br><br>';

    $user = $app['security']->getToken()->getUser();

    if ('anon.' === $user) {
        $text .= 'No estas identificat (ets un usuari anònim)';
    } else {
        $text .= 'Estàs identificat com a ' . $user->getUsername();
        $text .= '<br><br>' . print_r($user, true);
    }

    return $text;
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

$app->post('/register', function (Request $request) use ($app) {
    $email = $request->request->get('email');
    $rawPassword = $request->request->get('password');

    $encoder = new \Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder();
    $salt = md5(uniqid(true));
    $password = $encoder->encodePassword($rawPassword, $salt);

    $newUser = new \Hospi\Model\User($email, $password, md5(uniqid(true)));
    $now = new \DateTime();
    $app['db']->insert('users', array(
        'email' => $email,
        'password' => $password,
        'salt' => $salt,
        'roles' => \Hospi\Model\User::ROLE_USER,
        'created_at' => $now->format('Y-m-d H:i:s')
    ));

    return $app->redirect('/');
});

$app->get('/register', function (Request $request) use ($app) {
    return $app['twig']->render('register.html.twig');
});

return $app;
