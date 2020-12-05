<?php

use app\controllers\AppController;
use app\extensions\TwigCsrf;
use app\extensions\TwigMessages;
use app\helpers\Auth;
use app\middlewares\AuthMiddleware;
use app\middlewares\GuestMiddleware;
use app\middlewares\OldInputMiddleware;
use Illuminate\Database\Capsule\Manager as DB;
use Slim\App;
use Slim\Csrf\Guard;
use Slim\Flash\Messages;
use Slim\Http\Environment;
use Slim\Http\Uri;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

require_once(__DIR__ . '/vendor/autoload.php');

session_start();
date_default_timezone_set('Europe/Paris');

$env = Dotenv\Dotenv::createImmutable(__DIR__);
$env->load();
$env->required(['DB_DRIVER', 'DB_HOST', 'DB_NAME', 'DB_USER', 'DB_PWD', 'DB_CHARSET', 'DB_COLLATION', 'DB_PREFIX']);

$db = new DB();
$db->addConnection([
    'driver' => $_ENV['DB_DRIVER'],
    'host' => $_ENV['DB_HOST'],
    'database' => $_ENV['DB_NAME'],
    'username' => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PWD'],
    'charset' => $_ENV['DB_CHARSET'],
    'collation' => $_ENV['DB_COLLATION'],
    'prefix' => $_ENV['DB_PREFIX']
]);
$db->setAsGlobal();
$db->bootEloquent();

$app = new App();

$container = $app->getContainer();
$container['uploadsPath'] = __DIR__ . DIRECTORY_SEPARATOR . 'uploads';
$container['csrf'] = function () {
    $guard = new Guard();
    $guard->setPersistentTokenMode(true);
    return $guard;
};
$container['flash'] = function () {
    return new Messages();
};
$container['view'] = function ($container) {
    $view = new Twig(__DIR__ . '/app/views', [
        'cache' => false
    ]);

    $view->getEnvironment()->addGlobal('auth', [
        'check' => Auth::check(),
        'user' => Auth::user()
    ]);

    $view->addExtension(new TwigExtension($container->router, Uri::createFromEnvironment(new Environment($_SERVER))));
    $view->addExtension(new TwigMessages(new Messages()));
    $view->addExtension(new TwigCsrf($container->csrf));
    return $view;
};

$app->add(new OldInputMiddleware($container));
$app->add($container->csrf);

$app->get('/', AppController::class . ':showHome')->setName('home');
$app->get('/about', AppController::class . ':showAbout')->setName('about');
$app->get('/contact', AppController::class . ':showContact')->setName('contact');
$app->get('/work', AppController::class . ':showWork')->setName('work');

$app->get('/file/xhr/get', function (){
    echo \app\models\File::all();
})->setName('getFile');

$app->post('/sendmail', AppController::class . ':sendMail')->setName('sendMail');

$app->group('', function() {
    $this->get('/adminsecretgang/login', AppController::class . ':showLogin')->setName('showLogin');

    $this->post('/login', AppController::class . ':login')->setName('login');
})->add(new GuestMiddleware($container));

$app->group('', function () {
    $this->get('/adminsecretgang/account', AppController::class . ':showAccount')->setName('showAccount');

    $this->post('/upload/add', AppController::class . ':uploadFile')->setName('uploadFile');
    $this->post('/update/media', AppController::class . ':updateMedia')->setName('updateMedia');
    $this->post('/delete/media', AppController::class . ':deleteMedia')->setName('deleteMedia');

    $this->post('/update/mail', AppController::class . ':updateMail')->setName('updateMail');
    $this->post('/update/pseudo', AppController::class . ':updatePseudo')->setName('updatePseudo');
    $this->post('/update/password', AppController::class . ':updatePassword')->setName('updatePassword');

    $this->post('/logout', AppController::class . ':logout')->setName('logout');
})->add(new AuthMiddleware($container));


$app->run();