<?php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once 'vendor/autoload.php';

session_start();

use Slim\Http\Request;
use Slim\Http\Response;

// create a log channel
$log = new Logger('main');
$log->pushHandler(new StreamHandler(dirname(__FILE__) . '/logs/everything.log', Logger::DEBUG));
$log->pushHandler(new StreamHandler(dirname(__FILE__) . '/logs/errors.log', Logger::ERROR));

// authentication information and client's IP address in the log
$log->pushProcessor(function ($record) {
    $record['extra']['user'] = isset($_SESSION['user']) ? $_SESSION['user']['userName'] : '=anonymous=';
    $record['extra']['ip'] = $_SERVER['REMOTE_ADDR'];
    return $record;
});

if (strpos($_SERVER['HTTP_HOST'], "ipd24.com") !== false) {
    // hosting on ipd24.com
    DB::$dbName = 'cp5003_parkbud1';
    DB::$user = 'cp5003_parkbud1';
    DB::$password = 'vwKcGKnNDwIc';
} else { // local computer
    DB::$dbName = 'parkbud';
    DB::$user = 'parkbud';
    DB::$host = 'localhost';
    DB::$password = ']sKM*hpv[B65cs*4';
    DB::$port = 3333;
}

// Internal Error Handler
DB::$error_handler = 'db_error_handler'; // runs on mysql query errors
DB::$nonsql_error_handler = 'db_error_handler'; // runs on library errors (bad syntax, etc)

function db_error_handler($params) {
    global $log;
    // log first
    $log->error("Database error: " . $params['error']);
    if (isset($params['query'])) {
        $log->error("SQL query: " . $params['query']);
    }
    // redirect
    header("Location: /internalerror");
    die;
}

// Create and configure Slim app
$config = ['settings' => [
    'addContentLengthHeader' => false,
    'displayErrorDetails' => true
]];
$app = new \Slim\App($config);

// Fetch DI Container
$container = $app->getContainer();
$container['upload_directory'] = __DIR__ . '/uploads';
// Register Twig View helper
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig(dirname(__FILE__) . '/templates', [
        'cache' => dirname(__FILE__) . '/tmplcache',
        'debug' => true, // This line should enable debug mode
    ]);
    //
    $view->getEnvironment()->addGlobal('userSession', isset($_SESSION['user']) ? $_SESSION['user'] : null);
    // Instantiate and add Slim specific extension
    $router = $c->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new \Slim\Views\TwigExtension($router, $uri));
    return $view;
};

