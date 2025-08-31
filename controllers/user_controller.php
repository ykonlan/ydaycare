<?php 
include_once __DIR__."/../models/user.php";
include_once __DIR__."/../utils/form_renderer.php";

class UserController{
    public static function all_users(){
        [$page, $search] = get_page_and_search($_SERVER["REQUEST_URI"]);
        echo json_encode(User::all($page, $search));
    }

    public static function add_user(){
        if($_SERVER["REQUEST_METHOD"] === "GET"){
            return self::render_form();
        }
        $data = json_decode(file_get_contents("php://input"),TRUE);
        $is_valid = User::validate($data);
        if(!$is_valid){
            echo json_encode($is_valid);
        }
        $user_name = $data["user_name"];
        $user_email = $data["user_email"];
        $user_phone = $data["user_phone"];
        $user_password = $data["user_password"];
        $user_role = $data["user_role"];
        $user_class = $data["user_class"];
        $user_password = password_hash($user_password, PASSWORD_BCRYPT);
        echo json_encode(User::add($user_name, $user_email, $user_phone, $user_password, $user_role, $user_class));
    }

    public static function render_form(){
        echo json_encode(form_renderer("users"));
    }

    public static function get_user($id){
        echo json_encode(User::get($id));
    }

    public static function edit_user($id){
        if($_SERVER["REQUEST_METHOD"] === "GET"){
            return self::render_form();
        }
        $data = json_decode(file_get_contents("php://input"),TRUE);
        $user_name = $data["user_name"];
        $user_phone = $data["user_phone"];
        $user_email = $data["user_email"];
        echo json_encode(User::edit($user_name, $user_phone, $user_email, $id));
    }

    public static function delete_user($id){
        echo json_encode(Ward::delete($id));
    }


}


?>