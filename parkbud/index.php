<?php

// for development we want to see all the errors, some php.ini versions disable those (e.g. MAMP)
error_reporting(E_ALL);
ini_set('display_errors', 'On');

session_start();

require_once 'vendor/autoload.php';

require_once 'init.php';

//require_once 'account.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Create and configure Slim app
$config = ['settings' => [
    'addContentLengthHeader' => false,
]];
$app = new \Slim\App($config);

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

$app->get("/", function ($request, $response, $args) {
    global $twig;
    return $response->write($twig->render('index.html.twig', ['title' => 'Parkbud']));
});


// Run app
$app->run();
