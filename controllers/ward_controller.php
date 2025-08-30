<?php 
include_once __DIR__."/../models/ward.php";
include_once __DIR__."/../utils/uriparser.php";

class WardController{
    public static function all_wards(){
        [$page, $search] = get_page_and_search($_SERVER["REQUEST_URI"]);
        header("Content-Type: application/json");
        echo json_encode(Ward::all($page, $search));
    }

    public static function get_ward($id){
        header("Content-Type: application/json");
        echo json_encode(Ward::get($id));
    }

    public static function edit_ward($id){
        if($_SERVER["REQUEST_METHOD"] === "GET"){
            $form = Ward::render_form();
            $ward = Ward::get($id);
            header("Content-Type: application/json");
            echo json_encode(["form"=>$form,"existing"=>$ward]);
            return;
        }
        $raw = file_get_contents("php://input");
        $data = json_decode($raw, TRUE);
        $ward_name = $data["ward_name"];
        $ward_parent = $data["ward_parent"];
        $ward_class = $data["ward_class"];
        $ward_dob = $data["ward_dob"];
        $is_valid = Ward::validate($data);
        header("Content-Type: application/json");
        echo json_encode(["id"=>$id]);
        if(!isset($is_valid["error"])){
            echo json_encode(Ward::edit($ward_name, $ward_parent, $ward_dob, $ward_class, $id));
        }
        else{
            echo json_encode($is_valid);
    }
}

    public static function delete_ward($id){
        header("Content-Type: application/json");
        echo json_encode(Ward::delete($id));
    }

    public static function add_ward(){
        if($_SERVER["REQUEST_METHOD"] === "GET"){
            return self::get_ward_form(); 
        }
        $raw = file_get_contents("php://input");
        $data = json_decode($raw, TRUE);
        $ward_name = $data["ward_name"];
        $ward_parent = $data["ward_parent"];
        $ward_dob = $data["ward_dob"];
        $ward_class = $data["ward_class"];
        $is_valid = Ward::validate($data);
        header("Content-Type: application/json");
        if(!isset($is_valid["error"])){
            echo json_encode(Ward::add($ward_name, $ward_parent, $ward_dob, $ward_class));
        }
        else{
            echo json_encode($is_valid);
        }
        
    }

    public static function get_ward_form(){
        header("Content-Type: application/json");
        echo json_encode(Ward::render_form());
    }
}
