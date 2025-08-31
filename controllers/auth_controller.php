<?php
include_once __DIR__. "/../models/user.php";

class AuthController{
    public static function login(){
        $data = json_decode(file_get_contents("php://input"),TRUE);
        $user_pw = $data["user_password"];
        $user_phone = $data["user_phone"];
        $response = User::login($user_phone, $user_pw);
        if(isset($response["error"])){
            return json_encode($response);
        }
        [$_SESSION["user_name"], $_SESSION["user_role"], $_SESSION["user_phone"]] = $response; 
        echo json_encode(["success"=>"Welcome, ".$_SESSION["user_name"]]);
    }
}

?>