<?php 
include_once "../models/ward.php";

class WardController{
    public static function all_wards(){
        $uri = $_SERVER["REQUEST_URI"]
        $query = parse_url($uri, PHP_URL_QUERY);
        $page = 
        $search = 
        return Ward::all($page, $search);
    }

    public static function get_ward($id){
        return Ward::get($id);
    }

    public static function edit_ward($id){
        $ward_name = $_POST['ward_name'];
        $ward_class = $_POST['ward_class'];
        $ward_parent = $_POST['parent_id'];
        header("Content-Type: application/json");
        return Ward::edit($ward_name, $ward_parent, $ward_class, $id);
    }

    public static function delete_ward($id){

    }
}