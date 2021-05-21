<?php
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

require_once 'vendor/autoload.php';
require_once 'init.php';

// $loader = new FilesystemLoader(__DIR__ . '/templates');
// $twig = new Environment($loader);

// STATE 1: first display
$app->get("/addrule", function ($request, $response, $args) {
    global $twig;
    // return $response->write($twig->render('register.html.twig', ['title' => 'Parkbud']));
    return $this->view->render($response, 'ruleadd.html.twig');
});

// Fetch DI Container
$container = $app->getContainer();

// File upload directory
$container['upload_directory'] = __DIR__ . '/uploads';


// STATE 2&3: receiving submission
$app->post("/addrule", function ($request, $response, $args) use ($log){
    if (!isset($_SESSION['user'])) { // refuse if user not logged in
        $response = $response->withStatus(403);
        return $this->view->render($response, 'error_access_denied.html.twig');
    }

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
    

    if ($errorList) {
        return $this->view->render($response, 'addrule.html.twig',
            [ 
                'errorList' => $errorList, 
                'v' => 
                [ 
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
        $userId = $_SESSION['user']['id'];
    
        DB::insert('addrule', [
            'userId'=> $userId,
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
        ]);

    global $twig;
    return $this->view->render($response, 'addrule_success.html.twig');
    // return $response->write($twig->render('addrule.html.twig', ['title' => 'Parkbud']));
    }
});

function verifyUploadedPhoto(Psr\Http\Message\UploadedFileInterface $photo, &$mime = null) {
    if ($photo->getError() != 0) {
        return "Error uploading photo " . $photo->getError();
    } 
    //if ($photo->getSize() > 1024*1024) { // 1MiB
    //    return "File too big. 1MB max is allowed.";
    // }
    $info = getimagesize($photo->file);
    if (!$info) {
        return "File is not an image";
    }
    // echo "\n\nimage info\n";
    // print_r($info);
    // if ($info[0] < 200 || $info[0] > 1000 || $info[1] < 200 || $info[1] > 1000) {
    //    return "Width and height must be within 200-1000 pixels range";
    // }
    $ext = "";
    switch ($info['mime']) {
        case 'image/jpeg': $ext = "jpg"; break;
        case 'image/gif': $ext = "gif"; break;
        case 'image/png': $ext = "png"; break;
        default:
            return "Only JPG, GIF and PNG file types are allowed";
    } 
    if (!is_null($mime)) {
        $mime = $info['mime'];
    }
    return TRUE;
}

use Slim\Http\UploadedFile;

// FIXME: this function should be allowed to fail, on moveTo or invalid extension
function moveUploadedFile($directory, UploadedFile $uploadedFile) {
    // Avoid a serious security flaw - user must not be ablet o upload .php file and exploit our server
    $extension = strtolower(pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION));
    if (!in_array($extension, ['jpg', 'jpeg', 'gif', 'png'])) {
        return FALSE;
    }
    $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    $filename = sprintf('%s.%0.8s', $basename, $extension);
    try {
        $uploadedFile->moveTo($directory . DIRECTORY_SEPARATOR . $filename); // FIXME catch exception on failure
    } catch (Exception $e) {
        // TODO: log the error message
        return FALSE;
    }
    return $filename;
}