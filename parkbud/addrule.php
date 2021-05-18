<?php





require_once 'vendor/autoload.php';
require_once 'init.php';








$app->get("/addrule", function ($request, $response, $args) use ($log){
/*
    $ruleLongitude = ;
    $ruleLatitute = ;
    $streetName = $request->getParam('streetname');
    $startDate = $request->getParam('startDate');
    $endDate = $request->getParam('endDate');
    //$parkMeter = $request->getParam('endDate');
    $ruleSide = ;
    $ruleDay = ;
    $ruleStartTime = $request->getParam('streetname');
    $ruleEndTime =;

    $errorList = [];



    */

    global $twig;
    return $response->write($twig->render('addrule.html.twig', ['title' => 'Parkbud']));
});