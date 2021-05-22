<?php

require_once 'vendor/autoload.php';
require_once 'init.php';

$app->get("/", function ($request, $response, $args) {
    $rulesList = DB::query("SELECT * FROM addrule");
    // print_r($rulesList);

    //[{streetName: '123 street', latitude: 55, longitude: 70},{streetName: '123 street', latitude: 55, longitude: 70}]
    $list = array();
    foreach ($rulesList as $rule) {
        $list[] = ['streetName' => $rule['streetName'], 'latitude' => $rule['latitude'], 'longitude' => $rule['longitude']];
    }
    // echo json_encode($list);
    // print_r($list);
    return $this->view->render($response, 'main.html.twig', ['title' => 'Parkbud.ca', 'rulesList' => $list]);
});

$app->get('/internalerror', function ($request, $response, $args) {
    return $this->view->render($response, 'error_internal.html.twig');
});
