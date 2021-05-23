<?php

use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once 'vendor/autoload.php';
require_once 'init.php';




// *** Register user ***

//STATE 1: first display
$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);
$app->get("/register", function ($request, $response, $args) {
    global $twig;
    // return $response->write($twig->render('register.html.twig', ['title' => 'Parkbud']));
    return $this->view->render($response, 'register.html.twig');
});

// STATE 2: receiving submission
$app->post('/register', function ($request, $response, $args) use ($log) {

    $firstName = $request->getParam('firstName');
    $lastName = $request->getParam('lastName');
    $userName = $request->getParam('userName');
    $email = $request->getParam('email');
    $phone = $request->getParam('phone');
    $pass1 = $request->getParam('pass1');
    $pass2 = $request->getParam('pass2');
    $city = $request->getParam('city');
    $street = $request->getParam('street');
    $province = $request->getParam('province');
    $postCode = $request->getParam('postCode');
    $isAgree = $request->getParam('isAgree');

    $errorList = [];

    // verify username
    $result = verifyUserName($userName);
    if ($result !== TRUE) { $errorList[] = $result; }

    // verify email
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
        $errorList [] = "Email does not look valid";
        $email = "";
    } else {
        // is email already in use?
        $record = DB::queryFirstRow("SELECT id FROM user WHERE email=%s", $email);
        if ($record) {
            array_push($errorList, "This email is already registered");
            $email = "";
        }
    }

    if (strlen($firstName) < 2 || strlen($firstName) > 50) {
        $errorList['firstName'] = "First Name must be 2-50 characters";
        $firstName = '';
    }
    if (strlen($lastName) < 2 || strlen($lastName) > 50) {
        $errorList['lastName'] = "Last Name must be 2-50 characters";
        $lastName = '';
    }

    if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $phone)) {
        $errorList['phone'] = "Phone: " . $phone . " must be in this format: 555-222-9999";
        $phone = '';
    }
    if (strlen($street) < 2 || strlen($street) > 100) {
        $errorList['street'] = "Street number and name must be 2-100 characters";
    }
    if (strlen($city) < 2 || strlen($city) > 100) {
        $errorList['city'] = "City must be 2-100 characters";
    }
    if (!isset($province)) {
        $errorList['province'] = "Province must be selected";
    }
    if(!preg_match("/^[A-Za-z0-9_ ]{3,4}[A-Za-z0-9]{3}$/", $postCode)) {
        $errorList['postalCode'] = "PostalCode: " . $postCode . " must be in XXX YYY format";
    }
    if (strcmp($isAgree, 'on') <> 0 ) {
        $errorList['isAgree'] = "Please agree to the terms and conditions before registeration";
    }

    // verify password
    $result = verifyPasswordQuailty($pass1, $pass2);
    if ($result != TRUE) { $errorList[] = $result; }

    
    // STATE 3: errors
    if ($errorList) {   // if has error(s)
        $log->error(sprintf("Register failed: email %s, username %s, uid=%d", $email, $userName, $_SERVER['REMOTE_ADDR']));
        return $this->view->render($response, 'register.html.twig', [
            'errors' => $errorList,
            'user' => [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'userName' => $userName,
                'phone' => $phone,
                'email' => $email,
                'pass1' => $pass1,
                'pass2' => $pass2,
                'street' => $street,
                'city' => $city,
                'province' => $province,
                'postalCode' => strtoupper($postCode)
            ]
        ]);
    } else {    // if registeration succeed
        //Password Encripton
        global $passwordPepper;
        $pwdPeppered = hash_hmac("sha256", $pass1, $passwordPepper);
        $pwdHashed = password_hash($pwdPeppered, PASSWORD_DEFAULT); // PASSWORD_ARGON2ID);
        DB::insert('user', [
            'firstName' => $firstName,
            'lastName' => $lastName,
            'userName' => $userName,
            'email' => $email,
            'password' => $pwdHashed,
            'phone' => $phone,
            'street' => $street,
            'city' => $city,
            'province' => $province,
            'postalCode' => strtoupper($postCode)
        ]);
        $_SESSION['user'] = DB::queryFirstRow("SELECT * FROM user WHERE email = %s",$email);
        $log->debug(sprintf("New user registered successfully: email %s, username %s, uid=%d", $email, $userName, $_SERVER['REMOTE_ADDR']));       
        // global $twig;
        // return $response->write($twig->render('register_success.html.twig', ['title' => 'Parkbud']));
        return $this->view->render($response, 'register_success.html.twig');
    }
});

// these functions return TRUE on success and string describing an issue on failure
function verifyUserName($name) {
    if (preg_match('/^[a-zA-Z0-9\ \\._\'"-]{4,50}$/', $name) != 1) { // no match
        return "Name must be 4-50 characters long and consist of letters, digits, "
            . "spaces, dots, underscores, apostrophies, or minus sign.";
    }
    return TRUE;
}

function verifyPasswordQuailty($pass1, $pass2) {
    if ($pass1 != $pass2) {
        return "Passwords do not match";
    } else {
        if ((strlen($pass1) < 6) || (strlen($pass1) > 100)
                || (preg_match("/[A-Z]/", $pass1) == FALSE )
                || (preg_match("/[a-z]/", $pass1) == FALSE )
                || (preg_match("/[0-9]/", $pass1) == FALSE )) {
            return "Password must be 6-100 characters long, "
                . "with at least one uppercase, one lowercase, and one digit in it";
        }
    }
    return TRUE;
}

// *** Login user ***
// STATE 1: first display
$app->get('/login', function ($request, $response, $args) {
    return $this->view->render($response, 'login.html.twig');
});

// STATE 2: receiving submission
$app->post('/login', function ($request, $response, $args) use ($log) {
    $errorList = [];
    $emailOrUsername = $request->getParam('emailOrUsername');
    $password = $request->getParam('password');
    $record = DB::queryFirstRow("SELECT id, email, password, userName, role FROM user WHERE (email=%s) OR (userName=%s)", $emailOrUsername, $emailOrUsername);
    $loginSuccess = false;
    if ($record) {
        global $passwordPepper;
        $pwdPeppered = hash_hmac("sha256", $password, $passwordPepper);
        $pwdHashed = $record['password'];
        if (password_verify($pwdPeppered, $pwdHashed)) {
            $loginSuccess = true;
        } else {
            $errorList[] = "Password is incorrect";
        }
    } else {
        $errorList[] = "Username or Email Address does not exist";
    }
    // STATE 3: errors
    if (!$loginSuccess) {
        $log->debug(sprintf("Login failed for email or username: %s and password: %s from %s", $emailOrUsername, $password, $_SERVER['REMOTE_ADDR']));
        return $this->view->render($response, 'login.html.twig', [ 'errors' => $errorList ]);
    } else {
        unset($record['password']); // for security reasons remove password from session
        $_SESSION['user'] = $record; // remember user logged in
        $log->debug(sprintf("Login successful for email or username: %s, uid=%d, from %s", $emailOrUsername, $record['id'], $_SERVER['REMOTE_ADDR']));
        setFlashMessage("Login Successfully");
        if(strcmp($record['role'],'user') === 0){
            return $response->withRedirect("/");
        } elseif (strcmp($record['role'],'admin') === 0){
            return $response->withRedirect("/");
        }
    }
});

// *** User logout  ***
$app->get('/logout', function ($request, $response, $args) use ($log) {
    if(isset($_SESSION['user'])){
        $log->debug(sprintf("Logout successful for uid=%d, from %s", @$_SESSION['user']['id'], $_SERVER['REMOTE_ADDR']));
        unset($_SESSION['user']);
        
        setFlashMessage("You have been logout!");
        return $response->withRedirect("/");
    }
});

// *** User Edits Profile  ***
//STATE 1: first display the existing account registration page
$app->get('/account', function ($request, $response, $args) use ($log){
    if(isset($_SESSION['user'])) {
        $user = DB::queryFirstRow("SELECT * FROM user WHERE id=%d", $_SESSION['user']['id']);
    }
    if(isset($user)) {
        $log->debug(sprintf("Trying to edit my account with userName %s, %s", $user['userName'], $_SERVER['REMOTE_ADDR']));
        return $this->view->render($response, 'register.html.twig',['user' => $user]);
    } else {
        $log->error(sprintf("Internal Error: Cannot find userName %s\n:%s", $_SESSION['user']['userName'], $_SERVER['REMOTE_ADDR']));
        return $response->withHeader("Location", "/error_internal",403);
    }
});

//STATE 2: receiving submission
$app->post('/account', function ($request, $response, $args) use ($log) {
    if(isset($_SESSION['user'])) {
        $originUser = DB::queryFirstRow("SELECT * FROM user WHERE id=%d", $_SESSION['user']['id']);
    }
    if(isset($originUser)) {
        $firstName = $request->getParam('firstName');
        $lastName = $request->getParam('lastName');
        $userName = $request->getParam('userName');
        $email = $request->getParam('email');
        $phone = $request->getParam('phone');
        $pass1 = $request->getParam('pass1');
        $pass2 = $request->getParam('pass2');
        $city = $request->getParam('city');
        $street = $request->getParam('street');
        $province = $request->getParam('province');
        $postCode = $request->getParam('postCode');
        $isAgree = $request->getParam('isAgree');

        $errorList = [];

        // verify username
        $result = verifyUserName($userName);
        if ($result !== TRUE) { $errorList[] = $result; }

        // verify email
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
            $errorList [] = "Email does not look valid";
            $email = "";
        } //else {
            // is email already in use?
        //    $record = DB::queryFirstRow("SELECT id FROM user WHERE email=%s", $email);
        //    if ($record) {
        //        array_push($errorList, "This email is already registered");
        //        $email = "";
        //    }
        // }

        if (strlen($firstName) < 2 || strlen($firstName) > 50) {
            $errorList['firstName'] = "First Name must be 2-50 characters";
            $firstName = '';
        }
        if (strlen($lastName) < 2 || strlen($lastName) > 50) {
            $errorList['lastName'] = "Last Name must be 2-50 characters";
            $lastName = '';
        }

        if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $phone)) {
            $errorList['phone'] = "Phone: " . $phone . " must be in this format: 555-222-9999";
            $phone = '';
        }
        if (strlen($street) < 2 || strlen($street) > 100) {
            $errorList['street'] = "Street number and name must be 2-100 characters";
        }
        if (strlen($city) < 2 || strlen($city) > 100) {
            $errorList['city'] = "City must be 2-100 characters";
        }
        if (!isset($province)) {
            $errorList['province'] = "Province must be selected";
        }
        if(!preg_match("/^[A-Za-z0-9_ ]{3,4}[A-Za-z0-9]{3}$/", $postCode)) {
            $errorList['postalCode'] = "PostalCode: " . $postCode . " must be in XXX YYY format";
        }
        if (strcmp($isAgree, 'on') <> 0 ) {
            $errorList['isAgree'] = "Please agree to the terms and conditions before registeration";
        }

        // verify password
        $result = verifyPasswordQuailty($pass1, $pass2);
        if ($result != TRUE) { $errorList[] = $result; }

        if ($errorList) {
            $log->error(sprintf("Account information change failed: email %s, username %s, uid=%d", $email, $userName, $_SERVER['REMOTE_ADDR']));
            return $this->view->render($response, 'register.html.twig', [
                'errors' => $errorList,
                'user' => [
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                    'username' => $userName,
                    'phone' => $phone,
                    'email' => $email,
                    'street' => $street,
                    'city' => $city,
                    'province' => $province,
                    'postalCode' => strtoupper($postCode)
                ]
            ]);
        } else {
            global $passwordPepper;
            $pwdPeppered = hash_hmac("sha256", $pass1, $passwordPepper);
            $pwdHashed = password_hash($pwdPeppered, PASSWORD_DEFAULT); // PASSWORD_ARGON2ID);

            $updateUser = ['firstName' => $firstName,
                'lastName' => $lastName,
                'userName' => $userName,
                'email' => $email,
                'password' => $pwdHashed,
                'phone' => $phone,
                'street' => $street,
                'city' => $city,
                'province' => $province,
                'postalCode' => strtoupper($postCode)];

            DB::update('user', $updateUser, "id=%d", $originUser['id']);
            // refresh new user data
            $_SESSION['user'] = DB::queryFirstRow("SELECT * FROM user WHERE id = %d",$originUser['id']);
            $log->debug(sprintf("Account is update uccessfully: new email %s, new userName %s, uid=%d", $_SESSION['user']['email'], $_SESSION['user']['userName'], $_SERVER['REMOTE_ADDR']));
            setFlashMessage("Update user account successfully");
            return $this->view->render($response, 'update_profile_success.html.twig');
        }

    } else {
        $log->error(sprintf("Internal Error: Cannot find userName %s\n:%s", $_SESSION['user']['userName'], $_SERVER['REMOTE_ADDR']));
        return $response->withHeader("Location", "/error_internal",403);
    }
});


// ADMIN INTERFACE CRUD OPERATIONS HANDLING: Parking rules

// User can view rules list
$app->get('/user/rules/list', function($request, $response, $args) use($log){
    if(isset($_SESSION['user'])) {
        $rulesList = DB::query("SELECT * FROM addrule WHERE userid=%d",  $_SESSION['user']['id']);
        $user = DB::queryFirstRow("SELECT * FROM user WHERE id=%d", $_SESSION['user']['id']);
    }
    if (isset($rulesList)) {
        $log->debug(sprintf("Trying to edit my rules with userName %s, %s", $user['userName'], $_SERVER['REMOTE_ADDR']));
        return $this->view->render($response, 'rules_list.html.twig',['rulesList'=>$rulesList]);
    } else {
        $log->error(sprintf("Internal Error: Cannot find userName %s\n:%s", $_SESSION['user']['userName'], $_SERVER['REMOTE_ADDR']));
        return $response->withHeader("Location", "/error_internal",403);
    }
});


// edit
$app->get('/user/rules/edit/{id:[0-9]+}', function($request, $response, $args) use($log){
    $rule = DB::queryFirstRow("SELECT * FROM addrule WHERE id=%d", $args['id']);
    if(!$rule){
        $response = $response->withStatus(404);
        return $this->view->render($response, '/not_found_error.html.twig');
    }

    // To show all rules
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


    return $this->view->render($response, '/rules_edit.html.twig', ['rule'=>$rule, 'rulesList' => $list]);
});

// User can edit parking rules
$app->post('/user/rules/edit/{id:[0-9]+}', function ($request, $response, $args) use ($log) {
    $originRule = DB::queryFirstRow("SELECT * FROM addrule WHERE id=%d", $args['id']);
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
    // how to preload existing image???
    // $uploadedImage = $request->getParam('image');
    // if($uploadedImage == null){
        $hasPhoto = false;
        $uploadedImage = $request->getUploadedFiles()['image'];
        if ($uploadedImage->getError() != UPLOAD_ERR_NO_FILE) { // was anything uploaded?
            // print_r($uploadedImage->getError());
            $hasPhoto = true;
            $result = verifyUploadedPhoto($uploadedImage);
            if ($result !== TRUE) {
                $errorList[] = $result;
            } 
        }
    // }
    //

    $errorList = [];

/*
    // verify image
    $hasPhoto = false;
    $uploadedImage = $request->getUploadedFiles()['image'];
    if ($uploadedImage->getError() != UPLOAD_ERR_NO_FILE) { // was anything uploaded?
        // print_r($uploadedImage->getError());
        $hasPhoto = true;
        $result = verifyUploadedPhoto($uploadedImage);
        if ($result !== TRUE) {
            $errorList[] = $result;
        } 
    }
*/

    if ($errorList) {
        $log->error(sprintf("Edit rule failed: streetName %s, uid=%d", $streetName, $_SERVER['REMOTE_ADDR']));
        return $this->view->render($response, 'rules_edit.html.twig', [
            'errors' => $errorList,
            'user' => [
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
                
            ]
        ]);
    } else {
        if ($hasPhoto) {
            $directory = $this->get('upload_directory');
            $uploadedImagePath = moveUploadedFile($directory, $uploadedImage);
            if ($uploadedImagePath == FALSE) {
                return $response->withRedirect("/internalerror", 301);
            }
        }    

            $updateUser = [
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
                'image' => $uploadedImagePath
            ];

            DB::update('addrule', $updateUser, "id=%d", $originRule['id']);
            $log->debug(sprintf("User updated rule: id=%s successfully:  uid=%d", $originRule['id'], $_SERVER['REMOTE_ADDR']));
            setFlashMessage("Updated rule successfully");
            return $response->withRedirect("/user/rules/list");
        }
    
});


// User can delete his/her parking rules
$app->get('/user/rules/delete/{id:[0-9]+}', function($request, $response, $args) use($log){
    if($args['id'] == 0){
        $response = $response->withStatus(404);
        return $this->view->render($response, '/error_notfound.html.twig');
    } else {
        $originRule = DB::queryFirstRow("SELECT * FROM addrule WHERE id=%d", $args['id']);
        if(!$originRule){
            $response = $response->withStatus(404);
            return $this->view->render($response, '/error_notfound.html.twig');
        }
        return $this->view->render($response, 'rule_delete.html.twig', ['rule'=>$originRule]);
    }
});

$app->post('/user/rules/delete/{id:[0-9]+}', function($request, $response, $args) use($log){
    if($args['id'] == 0){
        $response = $response->withStatus(404);
        return $this->view->render($response, '/error_notfound.html.twig');
    }else{
        $originUser = DB::queryFirstRow("SELECT * FROM addrule WHERE id=%d", $args['id']);
        if(!$originUser){
            $response = $response->withStatus(404);
            return $this->view->render($response, '/error_notfound.html.twig');
        }
        DB::delete('addrule', "id=%d", $args['id']);
        $log->debug(sprintf("User deleted rule id=%d successfully, id=%d", $args['id'], $_SERVER['REMOTE_ADDR']));
        setFlashMessage("Delete rule Successfully");
        return $response->withRedirect("/user/rules/list");
    }
});

// *** Any visitor/user can see contact page **
$app->get('/contact', function ($request, $response, $args) use ($log){  
        return $this->view->render($response, 'contact.html.twig');
});

// *** Any visitor/user can see about us page **
$app->get('/aboutus', function ($request, $response, $args) use ($log){  
    return $this->view->render($response, 'aboutus.html.twig');
});


