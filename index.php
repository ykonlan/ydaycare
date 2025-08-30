<?php 
include_once __DIR__. "/controllers/ward_controller.php";
include_once __DIR__. "/utils/form_renderer.php";
$routes = [
    "/wards/add" => ["WardController", "add_ward"],
    "/wards/get/(\d+)" => ["WardController", "get_ward"],
    "/wards/get" => ["WardController", "all_wards"],
    "/wards/edit/(\d+)" => ["WardController", "edit_ward"],
    "/wards/delete/(\d+)" => ["WardController", "delete_ward"],
];

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER["REQUEST_METHOD"];
$path = parse_url($uri, PHP_URL_PATH);




foreach($routes as $routePattern => [$controller, $action]){
    if(preg_match("#^$routePattern$#", $path, $matches)){
        if(class_exists($controller) && method_exists($controller, $action)){
            array_shift($matches);
            $matches = array_map("intval", $matches);
            return $controller::$action(...$matches);
        }
        else{
            header("Content-Type:application/json");
            http_response_code(404);
            echo json_encode(["error"=>"Method not found"]);
            return;
        }
    }
}

header("Content-Type:application/json");
http_response_code(404);
echo json_encode(["error"=>"Route not found"]);





?>