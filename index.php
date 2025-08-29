<?php 

$routes = [
    "/wards/add" => ["WardController", "add"]
];

$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER["REQUEST_METHOD"];
$path = parse_url($uri, PHP_URL_PATH);


if(array_key_exists($path, $routes)){
    [$controller, $action] = $routes[$path];
    if(class_exists($controller) && method_exists($controller, $action)){
        return $controller::$action();
    }
    else{
        http_response_code(404);
        return ["error"=>"Method not found"];
    }
}
else{
    http_response_code(404);
        return ["error"=>"Route not found"];
}





?>