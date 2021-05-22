<?php

require_once 'vendor/autoload.php';
require_once 'init.php';

$app->get("/", function ($request, $response, $args) {
    $rulesList = DB::queryAllLists("SELECT * FROM addrule");


    //[{streetName: '123 street', latitude: 55, longitude: 70},{streetName: '123 street', latitude: 55, longitude: 70}]
    $list = array();

    foreach ($rulesList as $rule) {
        $r = [];
        $r['streetName'] = $rule[2];
        $r['latitude'] = $rule[21];
        $r['longitude'] = $rule[22];

        array_push($list, $r);
    }
    echo json_encode($list);
    return $this->view->render($response, 'main.html.twig', ['title' => 'Parkbud.ca', 'rulesList' => json_encode($list)]);
});

$app->get('/internalerror', function ($request, $response, $args) {
    return $this->view->render($response, 'error_internal.html.twig');
});
