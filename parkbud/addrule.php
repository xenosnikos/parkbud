<?php



use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

require_once 'vendor/autoload.php';
require_once 'init.php';

use Twig\Environment;
use Twig\Loader\FilesystemLoader;







$app->get("/addrule", function ($request, $response, $args) use ($log){

    $ruleLongitude = ;
    $ruleLatitute = ;
    $streetName = $request->getParam('streetname');
    $startDate = $request->getParam('startDate');
    $endDate = $request->getParam('endDate');
    //$parkMeter = $request->getParam('endDate');
    $ruleSide = ;
    $ruleDay = ;
    $ruleStartTime =;
    $ruleEndTime =;

    $errorList = [];



    global $twig;
    return $response->write($twig->render('addrule.html.twig', ['title' => 'Parkbud']));
});