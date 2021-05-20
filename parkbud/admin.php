<?php
    require_once 'vendor/autoload.php';
    require_once 'init.php';
    // require_once 'account.php';

    use Slim\Http\UploadedFile;

    // ADMIN INTERFACE EXAMPLE CRUD OPERATIONS HANDLING

    // Users list
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
                // setFlashMessage("Update user account successfully");
                return $response->withRedirect("/admin/users/list");
            }
    });

    //

    // equipment
    // list
    $app->get('/admin/equipments/list', function($request, $response, $args){
        $equipsList = DB::query("SELECT * FROM equipments");
        return $this->view->render($response, 'admin/equips_list.html.twig',['equipmentList'=>$equipsList]);
    });

    // edit
    $app->get('/admin/equips/edit/{id:[0-9]+}', function ($request, $response, $args) use($log){
        if($args['id'] == 0){
            return $this->view->render($response, 'admin/equip_edit.html.twig');
        }else{
            $originEquipment = DB::queryFirstRow("SELECT * FROM equipments WHERE id=%d", $args['id']);
            if(!$originEquipment){
                $response = $response->withStatus(404);
                return $this->view->render($response, '/error_notfound.html.twig');
            }
            return $this->view->render($response, 'admin/equip_edit.html.twig', ['equipment'=>$originEquipment]);
        }
    });

    $app->get('/admin/equips/delete/{id:[0-9]+}', function($request, $response, $args) use($log){
        if($args['id'] == 0){
            $response = $response->withStatus(404);
            return $this->view->render($response, '/error_notfound.html.twig');
        }else{
            $originEquipment = DB::queryFirstRow("SELECT * FROM equipments WHERE id=%d", $args['id']);
            if(!$originEquipment){
                $response = $response->withStatus(404);
                return $this->view->render($response, '/error_notfound.html.twig');
            }
            return $this->view->render($response, 'admin/equip_delete.html.twig', ['equipment'=>$originEquipment]);
        }
    });

    $app->post('/admin/equips/delete/{id:[0-9]+}', function($request, $response, $args) use($log){
        if($args['id'] == 0){
            $response = $response->withStatus(404);
            return $this->view->render($response, '/error_notfound.html.twig');
        }else{
            $originEquipment = DB::queryFirstRow("SELECT * FROM equipments WHERE id=%d", $args['id']);
            if(!$originEquipment){
                $log->error(sprintf("NO found equipment id=%d to delete, uid=%d", $args['id'], $_SERVER['REMOTE_ADDR']));
                $response = $response->withStatus(404);
                return $this->view->render($response, '/error_notfound.html.twig');
            }
            DB::delete('equipments', "id=%d", $args['id']);
            $log->debug(sprintf("Delete equipment id=%d successfully, uid=%d", $args['id'], $_SERVER['REMOTE_ADDR']));
            setFlashMessage("Delete equipment Successfully");
            return $response->withRedirect("/admin/equipments/list");
        }
    });

$app->get('/admin/users/delete/{id:[0-9]+}', function($request, $response, $args) use($log){
    if($args['id'] == 0){
        $response = $response->withStatus(404);
        return $this->view->render($response, '/error_notfound.html.twig');
    }else{
        $originUser = DB::queryFirstRow("SELECT * FROM users WHERE id=%d", $args['id']);
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
        $originUser = DB::queryFirstRow("SELECT * FROM users WHERE id=%d", $args['id']);
        if(!$originUser){
            $response = $response->withStatus(404);
            return $this->view->render($response, '/error_notfound.html.twig');
        }
        DB::delete('users', "id=%d", $args['id']);
        $log->debug(sprintf("Delete user id=%d successfully, uid=%d", $args['id'], $_SERVER['REMOTE_ADDR']));
        setFlashMessage("Delete user Successfully");
        return $response->withRedirect("/admin/users/list");
    }
});

    $app->post('/admin/equips/edit/{id:[0-9]+}', function ($request, $response, $args) use ($log) {

        $equipName = $request->getParam('equipname');
        $category = $request->getParam('category');
        $rateByMonth = $request->getParam('rateByMonth');
        $rateBySeason = $request->getParam('rateBySeason');
        $itemsInStock = $request->getParam('itemsInStock');
        $description = $request->getParam('description');

        $errorList = [];
        if (strlen($description) < 2 || strlen($description) > 1000) {
            $errorList[] = "Product description must be 2-1000 characters long";
        }
        if (preg_match('/^[a-zA-Z0-9 ,\.-]{2,100}$/', $equipName) !== 1) {
            $errorList[] = "Product's name must be 2-100 characters long made up of letters, digits, space, comma, dot, dash";
        }
        if (!is_numeric($itemsInStock) || $itemsInStock < 0 || $itemsInStock > 100000) {
            $errorList[] = "In-stock must be a number";
            $log->debug("In-Stock must be a number between 0 and 100000");
        }
        if (!is_numeric($rateByMonth) || $rateByMonth < 0 || $rateByMonth > 100000) {
            $errorList[] = "RateByMonth must be a number";
            $log->debug("RateByMonth must be a number between 0 and 100000");
        }
        if (!is_numeric($rateBySeason) || $rateBySeason < 0 || $rateBySeason > 100000) {
            $errorList[] = "RateBySeason must be a number";
            $log->debug("RateBySeason must be a number between 0 and 100000");
        }
        if ($category == 'Choose...') {
            $errorList[] = "You have to select product category";
            $log->debug(" product category is Null");
        }
        // Verify image
        $uploadedImagePath = null;
        $uploadedImage = $request->getUploadedFiles()['image'];
        if ($uploadedImage->getError() != UPLOAD_ERR_NO_FILE) {
            $result = verifyUploadedPhoto($uploadedImagePath, $uploadedImage);
            if ($result !== TRUE) {
                $errorList[] = $result;
            }
        }

        $valuesList = [
            'description' => $description, 'equipName' => $equipName,
            'rateByMonth' => $rateByMonth, 'rateBySeason' => $rateBySeason,
            'category' => $category, 'inStock' => $itemsInStock
        ];
        if ($errorList) { // STATE 2: errors - redisplay the form
            $log->error(sprintf("Update equipment id=%s failed by %s: uid=%d", $args['id'], $_SESSION['username'], $_SERVER['REMOTE_ADDR']));
            return $this->view->render($response, 'admin/equip_edit.html.twig', ['errors' => $errorList, 'equipment' => $valuesList]);
        } else { // STATE 3: success
            if ($uploadedImagePath != null) {
                $directory = $this->get('upload_directory');
                $uploadedImagePath = moveUploadedFile($directory, $uploadedImage);
            }
            $newEquip = ['description' => $description, 'equipName' => $equipName, 'rateByMonth' => $rateByMonth, 'rateBySeason' => $rateBySeason,
                'category' => $category, 'inStock' => $itemsInStock, 'photo' => $uploadedImagePath];
            if($args['id'] == 0){
                DB::insert('equipments', $newEquip);
                $log->debug(sprintf("Add new equipment successfully by %s: uid=%d", $_SESSION['username'], $_SERVER['REMOTE_ADDR']));
            }else{
                $originEquipment = DB::queryFirstRow("SELECT * FROM equipments WHERE id=%d", $args['id']);
                DB::update('equipments', $newEquip, "id=%d", $originEquipment['id']);
                $log->debug(sprintf("Update equipment successfully by %s: uid=%d", $_SESSION['username'], $_SERVER['REMOTE_ADDR']));
                setFlashMessage("Update equipment successfully");
            }
            return $response->withRedirect("/admin/equipments/list");
        }
    });

    // returns TRUE on success
    // returns a string with error message on failure
    function verifyUploadedPhoto(&$photoFilePath, $photo)
    {

        if ($photo->getError() != 0) {
            return "Error uploading photo " . $photo['error'];
        }
        if ($photo->getSize() > 1024 * 1024) { // 1MB
            return "File too big. 1MB max is allowed.";
        }
        $info = getimagesize($photo->file);
        if (!$info) {
            return "File is not an image";
        }
        // echo "\n\nimage info\n";
        // print_r($info);
        if ($info[0] < 130 || $info[0] > 1000 || $info[1] < 130 || $info[1] > 1000) {
            return "Width and height must be within 200-1000 pixels range";
        }
        $ext = "";
        switch ($info['mime']) {
            case 'image/jpeg':
                $ext = "jpg";
                break;
            case 'image/gif':
                $ext = "gif";
                break;
            case 'image/png':
                $ext = "png";
                break;
            default:
                return "Only JPG, GIF and PNG file types are allowed";
        }
        $baseName = "aaa";
        $photoFilePath = "uploads/" . $baseName . "." . $ext;

        return TRUE;
    }

    function moveUploadedFile($directory, UploadedFile $uploadedFile)
    {
        $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
        $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
        $filename = sprintf('%s.%0.8s', $basename, $extension);

        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename);

        return $filename;
    }

    // orders
    // list
    $app->get('/admin/orders/list', function($request, $response, $args){
        // TODO: JOIN TABLE ORDERS WITH ORDER ITEMS BY ORDER ID AND JOIN TABLE EQUIPMENTS WITH EQUIPEMENT ID AND JOIN USERS TABLE WITH USER ID
        $equipsList = DB::query("SELECT * FROM equipments");
        return $this->view->render($response, 'admin/orders_list.html.twig',['equipmentList'=>$equipsList]);
    });

    // refund



