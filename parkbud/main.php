<?php

require_once 'vendor/autoload.php';
require_once 'init.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

// Create and configure Slim app
$config = ['settings' => [
    'addContentLengthHeader' => false,
]];
$app = new \Slim\App($config);

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

$app->get("/main", function ($request, $response, $args) {
    global $twig;
    return $response->write($twig->render('main.html.twig', ['title' => 'Parkbud']));
});

//$app->get("/register", function ($request, $response, $args) {
//    global $twig;
//    return $response->write($twig->render('register.html.twig', ['title' => 'Parkbud']));
//});