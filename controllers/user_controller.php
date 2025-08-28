<?php 
include_once "../models/user.php";

class UserController{
    public static function all_users(){
        [$page, $search] = get_page_and_search($_SERVER["REQUEST_URI"]);
        return json_encode(User::all($page, $search));
    }
}


?>