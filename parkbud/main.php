<?php

require_once 'vendor/autoload.php';
require_once 'init.php';

$app->get("/", function ($request, $response, $args) {
    $rulesList = DB::query("SELECT * FROM addrule");
    // print_r($rulesList);

    //[{streetName: '123 street', latitude: 55, longitude: 70},{streetName: '123 street', latitude: 55, longitude: 70}]
    $list = array();
    foreach ($rulesList as $rule) {
        $list[] = ['streetName' => $rule['streetName'],
         'latitude' => $rule['latitude'], 
         'longitude' => $rule['longitude'], 
         'image' => $rule['image'], 
         'periodStart' => $rule['periodStart'],
         'periodEnd' => $rule['periodEnd'],
         'parkingMeter' => $rule['parkingMeter'],
         'sideFlag' => $rule['sideFlag'],
         'mondayStart' => $rule['mondayStart'],
         'mondayEnd' => $rule['mondayEnd'],
         'tuesdayStart' => $rule['tuesdayStart'],
         'tuesdayEnd' => $rule['tuesdayEnd'],
         'wednesdayStart' => $rule['wednesdayStart'],
         'wednesdayEnd' => $rule['wednesdayEnd'],
         'thursdayStart' => $rule['thursdayStart'],
         'thursdayEnd' => $rule['thursdayEnd'],
         'fridayStart' => $rule['fridayStart'],
         'fridayEnd' => $rule['fridayEnd'],
         'saturdayStart' => $rule['saturdayStart'],
         'saturdayEnd' => $rule['saturdayEnd'],
         'sundayStart' => $rule['sundayStart'],
         'sundayEnd' => $rule['sundayEnd'],
         'createdTS' => $rule['createdTS']
        ];
    }


    // $usersList = DB::query("SELECT * FROM user");
    // print_r($rulesList);

    //[{streetName: '123 street', latitude: 55, longitude: 70},{streetName: '123 street', latitude: 55, longitude: 70}]

    // $userlist = array();
    // foreach ($usersList as $user) {
    //    $userlist[] = ['firstName' => $rule['firstName'],
    //     'lastName' => $rule['lastName'], 
    //     'userName' => $rule['userName'], 
    //     'email' => $rule['email'], 
    //     'password' => $rule['password'],
    //     'phone' => $rule['phone'],
    //     'street' => $rule['street'],
    //     'city' => $rule['city'],
    //     'province' => $rule['province'],
    //     'postalCode' => $rule['postalCode']
    //    ];
    // }
    // echo json_encode($list);
    // print_r($list);
    return $this->view->render($response, 'main.html.twig', ['title' => 'Parkbud.ca', 'rulesList' => $list]);
});

$app->get('/internalerror', function ($request, $response, $args) {
    return $this->view->render($response, 'error_internal.html.twig');
 });
