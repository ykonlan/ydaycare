<?php 
include_once __DIR__."/../models/user.php";
include_once __DIR__."/../utils/form_renderer.php";

class UserController{
    public static function all_users(){
        [$page, $search] = get_page_and_search($_SERVER["REQUEST_URI"]);
        echo json_encode(User::all($page, $search));
    }

    public static function render_form(){
        echo json_encode(render_form("users"));
    }

    public static function get_user(){
        $id = get_id_from_path($_SERVER["REQUEST_URI"]);
        echo json_encode(Ward::get($id));
    }

    public static function edit_user(){
        $id = get_id_from_path($_SERVER["REQUEST_URI"]);
        echo json_encode(Ward::edit($id));
    }

    public static function delete_user(){
        $id = get_id_from_path($_SERVER["REQUEST_URI"]);
        echo json_encode(Ward::delete($id));
    }


}


?>