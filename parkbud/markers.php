
<?php

require_once 'vendor/autoload.php';
require_once 'init.php';

$app->get("/markers", function ($request, $response, $args) {
    $rulesList = DB::queryAllLists("SELECT * FROM addrule");


    //[{streetName: '123 street', latitude: 55, longitude: 70},{streetName: '123 street', latitude: 55, longitude: 70}]
    $list = array();

    foreach ($rulesList as $rule) {
        $r = [];
        $r['streetName'] = $rule[2];
        $r['latitude'] = $rule[22];
        $r['longitude'] = $rule[21];

        array_push($list, $r);
    }
    echo json_encode($list);
    return $this->view->render($response, 'markers.html.twig', ['title' => 'Parkbud.ca', 'rulesList' => json_encode($list)]);
});







