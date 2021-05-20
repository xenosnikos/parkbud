<?php

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once 'vendor/autoload.php';
require_once 'init.php';

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);
$app->get("/logout", function ($request, $response, $args) {

  global $twig;
  session_destroy();
  // return $response->write($twig->render('register.html.twig', ['title' => 'Parkbud']));
  return $this->view->render($response, 'addrule.html.twig');
});