<?php
    
    require_once 'vendor/autoload.php';
    require_once 'init.php';
 

    use Slim\Http\UploadedFile;

    // ADMIN INTERFACE CRUD OPERATIONS HANDLING: Users

    // Admin can view users list
    $app->get('/admin/users/list', function($request, $response, $args) use($log){
        $usersList = DB::query("SELECT * FROM user");
        return $this->view->render($response, 'admin/users_list.html.twig',['usersList'=>$usersList]);
    });

    // edit
    $app->get('/admin/users/edit/{id:[0-9]+}', function($request, $response, $args) use($log){
        $user = DB::queryFirstRow("SELECT * FROM user WHERE id=%d", $args['id']);
        if(!$user){
            $response = $response->withStatus(404);
            return $this->view->render($response, '/not_found_error.html.twig');
        }
        return $this->view->render($response, 'admin/users_edit.html.twig', ['user'=>$user]);
    });

 // Admin can edit users profile
 $app->post('/admin/users/edit/{id:[0-9]+}', function ($request, $response, $args) use ($log) {

    $originUser = DB::queryFirstRow("SELECT * FROM user WHERE id=%d", $args['id']);
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

    $errorList = [];

    // verify username
    $result = verifyUserName($userName);
    if ($result !== TRUE) { $errorList[] = $result; }

    // verify email
    if (filter_var($email, FILTER_VALIDATE_EMAIL) === FALSE) {
        $errorList [] = "Email does not look valid";
        $email = "";
    }

    $errorList = [];
    if (strlen($firstName) < 2 || strlen($firstName) > 50) {
        $errorList['firstName'] = "First Name must be 2-50 characters long";
        $firstName = '';
    }
    if (strlen($lastName) < 2 || strlen($lastName) > 50) {
        $errorList['lastName'] = "Last Name must be 2-50 characters long";
        $lastName = '';
    }
    if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $phone)) {
        $errorList['phone'] = "Phone: " . $phone . " must be like ***-***-****";
        $phone = '';
    }
    if (filter_var($email, FILTER_VALIDATE_EMAIL) == false) {
        $errors['email'] = "Invalid Email";
        $email = '';
    }
    if (strlen($street) < 2 || strlen($street) > 100) {
        $errorList['street'] = "Street must be 2-100 characters long";
    }
    if (strlen($city) < 2 || strlen($city) > 100) {
        $errorList['city'] = "City must be 2-100 characters long";
    }
    if (!isset($province)) {
        $errorList['province'] = "Province must be provided";
    }
    if(!preg_match("/^[A-Za-z0-9]{3} [A-Za-z0-9]{3}$/", $postCode)) {
        $errorList['postalCode'] = "PostalCode: " . $postCode . " must be in XXX YYY format";
    }

    // verify password
    $result = verifyPasswordQuailty($pass1, $pass2);
    if ($result != TRUE) { $errorList[] = $result; }

    if ($errorList) {
        $log->error(sprintf("Account information change failed: email %s, username %s, uid=%d", $email, $userName, $_SERVER['REMOTE_ADDR']));
        return $this->view->render($response, '/admin/users_edit.html.twig', [
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
            $log->debug(sprintf("Admin updated user account: id=%s successfully:  uid=%d", $originUser['id'], $_SERVER['REMOTE_ADDR']));
            // setFlashMessage("Updated user account successfully");
            return $response->withRedirect("/admin/users/list");
        }
});

// Admin can delete users profile
$app->get('/admin/users/delete/{id:[0-9]+}', function($request, $response, $args) use($log){
    if($args['id'] == 0){
        $response = $response->withStatus(404);
        return $this->view->render($response, '/error_notfound.html.twig');
    } else {
        $originUser = DB::queryFirstRow("SELECT * FROM user WHERE id=%d", $args['id']);
        if(!$originUser){
            $response = $response->withStatus(404);
            return $this->view->render($response, '/error_notfound.html.twig');
        }
        return $this->view->render($response, 'admin/user_delete.html.twig', ['user'=>$originUser]);
    }
});

$app->post('/admin/users/delete/{id:[0-9]+}', function($request, $response, $args) use($log){
    if($args['id'] == 0){
        $response = $response->withStatus(404);
        return $this->view->render($response, '/error_notfound.html.twig');
    }else{
        $originUser = DB::queryFirstRow("SELECT * FROM user WHERE id=%d", $args['id']);
        if(!$originUser){
            $response = $response->withStatus(404);
            return $this->view->render($response, '/error_notfound.html.twig');
        }
        DB::delete('user', "id=%d", $args['id']);
        $log->debug(sprintf("User deleted user id=%d successfully, uid=%d", $args['id'], $_SERVER['REMOTE_ADDR']));
        // setFlashMessage("Delete user Successfully");
        return $response->withRedirect("/admin/users/list");
    }
});


// ADMIN INTERFACE CRUD OPERATIONS HANDLING: Parking rules

// Admin can view rules list
$app->get('/admin/rules/list', function($request, $response, $args) use($log){
    $rulesList = DB::query("SELECT * FROM addrule");
    return $this->view->render($response, 'admin/rules_list.html.twig',['rulesList'=>$rulesList]);
});

// edit
$app->get('/admin/rules/edit/{id:[0-9]+}', function($request, $response, $args) use($log){
    $rule = DB::queryFirstRow("SELECT * FROM addrule WHERE id=%d", $args['id']);
    if(!$rule){
        $response = $response->withStatus(404);
        return $this->view->render($response, '/not_found_error.html.twig');
    }
    return $this->view->render($response, 'admin/rules_edit.html.twig', ['rule'=>$rule]);
});

// Admin can edit parking rules
$app->post('/admin/rules/edit/{id:[0-9]+}', function ($request, $response, $args) use ($log) {
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
    $uploadedImage = $request->getParam('image');
    if($uploadedImage == null){
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
    }
    $errorList = [];

    // verify image
    

    if ($errorList) {
        $log->error(sprintf("Edit rule failed: streetName %s, uid=%d", $streetName, $_SERVER['REMOTE_ADDR']));
        return $this->view->render($response, '/admin/rules_edit.html.twig', [
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
            $log->debug(sprintf("Admin updated rule: id=%s successfully:  uid=%d", $originRule['id'], $_SERVER['REMOTE_ADDR']));
            // setFlashMessage("Updated rule successfully");
            return $response->withRedirect("/admin/rules/list");
        }
    
});

// Admin can delete parking rules
$app->get('/admin/rules/delete/{id:[0-9]+}', function($request, $response, $args) use($log){
    if($args['id'] == 0){
        $response = $response->withStatus(404);
        return $this->view->render($response, '/error_notfound.html.twig');
    } else {
        $originRule = DB::queryFirstRow("SELECT * FROM addrule WHERE id=%d", $args['id']);
        if(!$originRule){
            $response = $response->withStatus(404);
            return $this->view->render($response, '/error_notfound.html.twig');
        }
        return $this->view->render($response, 'admin/rule_delete.html.twig', ['rule'=>$originRule]);
    }
});

$app->post('/admin/rules/delete/{id:[0-9]+}', function($request, $response, $args) use($log){
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
        $log->debug(sprintf("Admin deleted rule id=%d successfully, uid=%d", $args['id'], $_SERVER['REMOTE_ADDR']));
        // setFlashMessage("Delete user Successfully");
        return $response->withRedirect("/admin/rules/list");
    }
});
