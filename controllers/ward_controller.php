<?php 
include_once "../models/ward.php";
include_once "../utils/uriparser.php";

class WardController{
    public static function all_wards(){
        [$page, $search] = get_page_and_search($_SERVER["REQUEST_URI"]);
        header("Content-Type: application/json");
        return json_encode(Ward::all($page, $search));
    }

    public static function get_ward(){
        $id = get_id_from_path($_SERVER["REQUEST_URI"]);
        header("Content-Type: application/json");
        return json_encode(Ward::get($id));
    }

    public static function edit_ward(){
        $id = get_id_from_path($_SERVER["REQUEST_URI"]);
        $ward_name = $_POST['ward_name'];
        $ward_class = $_POST['ward_class'];
        $ward_parent = $_POST['parent_id'];
        header("Content-Type: application/json");
        return json_encode(Ward::edit($ward_name, $ward_parent, $ward_class, $id));
    }

    public static function delete_ward(){
        $id = get_id_from_path($_SERVER["REQUEST_URI"]);
        header("Content-Type: application/json");
        return json_encode(Ward::delete($id));


    }
}