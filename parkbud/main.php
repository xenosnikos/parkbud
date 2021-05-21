<?php

require_once 'vendor/autoload.php';
require_once 'init.php';

$app->get("/", function ($request, $response, $args) {
    global $twig;

    // $lat = DB::query("SELECT latitude FROM addrule");
    // $long = DB::query("SELECT longitude FROM addrule");

    // $jsonLat = json_encode($lat);
    // $jsonLong = json_encode($long);
    // echo $jsonLat;
    // echo $jsonLong;



    // $row = mysqli_fetch_array($sql, MYSQLI_BOTH);
    // $result = mysqli_query($sql, MYSQLI_BOTH);
    // while ($row = mysqli_fetch_array($result)) {
    //     echo "<p>" . $row['latitude'] . "</p>";
    // }
    return $this->view->render($response, 'main.html.twig', ['title' => 'Parkbud']);
});

$app->get('/internalerror', function ($request, $response, $args) {
    return $this->view->render($response, 'error_internal.html.twig');
});
