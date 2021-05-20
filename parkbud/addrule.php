<?php




use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once 'vendor/autoload.php';
require_once 'init.php';






$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);
$app->get("/addrule", function ($request, $response, $args) {
    global $twig;
    // return $response->write($twig->render('register.html.twig', ['title' => 'Parkbud']));
    return $this->view->render($response, 'addrule.html.twig');
});

$app->post("/addrule", function ($request, $response, $args) use ($log){
    
    $streetName = $request->getParam('streetName');
    $periodStart = $request->getParam('periodStart');
    $periodEnd = $request->getParam('periodEnd');
    $parkingMeter = $request->getParam('parkingMeter');
    $sideFlag = $request->getParam('sideFlag');
    $mondayStart = $request->getParam('mondayStart');
    $mondayEnd = $request->getParam('mondayEnd');
    $tuesdayStart = $request->getParam('tuesdayStart');
    $tuesdayEnd = $request->getParam('tuesdayEnd');
    $wednesdayStart = $request->getParam('wednesdayStart');
    $wednesdayEnd = $request->getParam('wednesdayEnd');
    $thursdayStart = $request->getParam('thursdayStart');
    $thursdayEnd = $request->getParam('thursdayEnd');
    $fridayStart = $request->getParam('fridayStart');
    $fridayEnd = $request->getParam('fridayEnd');
    $saturdayStart = $request->getParam('saturdayStart');
    $saturdayEnd = $request->getParam('saturdayEnd');
    $sundayStart = $request->getParam('sundayStart');
    $sundayEnd = $request->getParam('sundayEnd');
    $longitude = $request->getParam('longitude');
    $latitude = $request->getParam('latitude');
    $errorList = [];
    
    DB::insert('addrule', [
        'streetName' => $streetName,
        'periodStart' => $periodStart,
        'periodEnd' => $periodEnd,
        'parkingMeter' => $parkingMeter,
        'sideFlag' => $sideFlag,
        'mondayStart' => $mondayStart,
        'mondayEnd' => $mondayEnd,
        'tuesdayStart' => $tuesdayStart,
        'tuesdayEnd' => $tuesdayEnd,
        'wednesdayStart' => $wednesdayStart,
        'wednesdayEnd' => $wednesdayEnd,
        'thursdayStart' => $thursdayStart,
        'thursdayEnd' => $thursdayEnd,
        'fridayStart' => $fridayStart,
        'fridayEnd' => $fridayEnd,
        'saturdayStart' => $saturdayStart,
        'saturdayEnd' => $saturdayEnd,
        'sundayStart' => $sundayStart,
        'sundayEnd' => $sundayEnd,
        'longitude' => $longitude,
        'latitude' => $latitude,
    ]);

    global $twig;
    return $this->view->render($response, 'addrule.html.twig');
    // return $response->write($twig->render('addrule.html.twig', ['title' => 'Parkbud']));
});