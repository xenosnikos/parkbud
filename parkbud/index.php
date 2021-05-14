<?php

// for development we want to see all the errors, some php.ini versions disable those (e.g. MAMP)
error_reporting(E_ALL);
ini_set('display_errors', 'On');

session_start();

require_once 'vendor/autoload.php';

require_once 'init.php';

require_once 'account.php';


// Run app
$app->run();
