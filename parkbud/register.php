<?php

require_once 'vendor/autoload.php';
require_once 'init.php';



$app->get("/register", function ($request, $response, $args) {
    global $twig;
    return $response->write($twig->render('register.html.twig', ['title' => 'Parkbud']));
});