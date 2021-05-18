<?php

require_once 'vendor/autoload.php';
require_once 'init.php';


$app->get("/", function ($request, $response, $args) {
    global $twig;
    return $this->view->render($response, 'main.html.twig', ['title' => 'Parkbud']);
});

