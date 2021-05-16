<?php

require_once 'vendor/autoload.php';
require_once 'init.php';



$app->get("/addrule", function ($request, $response, $args) {
    global $twig;
    return $response->write($twig->render('addrule.html.twig', ['title' => 'Parkbud']));
});