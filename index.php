<?php 
include_once __DIR__. "/controllers/ward_controller.php";
$routes = [
    "/wards/add" => ["WardController", "add_ward"]
];

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER["REQUEST_METHOD"];
$path = parse_url($uri, PHP_URL_PATH);




if(array_key_exists($path, $routes)){
    [$controller, $action] = $routes[$path];
    header('Content-Type: application/json');
    if(class_exists($controller) && method_exists($controller, $action)){
        echo $controller::$action();
    }
    else{
        http_response_code(404);
        echo $controller;
        echo $action;
        echo json_encode(["error"=>"Method not found"]);
    }
}
else{
    http_response_code(404);
        echo json_encode(["error"=>"Route not found"]);
}





?>