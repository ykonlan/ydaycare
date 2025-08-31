<?php 
require_once(__DIR__."/../utils/db.php");

class User{
    public static function all($page,$search){
        try{
            $pdo = Database::get_connection();
            $limit = 20;
            $offset = ($page-1) * $limit;
            $sql = "SELECT * FROM users";
        $params = [];

        // Optional search filter across multiple fields
        if($search){
            $sql .= " WHERE LOWER(user_name) LIKE :search 
                      OR LOWER(user_email) LIKE :search 
                      OR LOWER(user_phone) LIKE :search";
            $params[':search'] = "%".strtolower(trim($search))."%";
        }

        // Add ordering and pagination
        $sql .= " ORDER BY user_name LIMIT :limit OFFSET :offset";

        $stmnt = $pdo->prepare($sql);

        // Bind search param if applicable
        if(isset($params[':search'])){
            $stmnt->bindValue(':search', $params[':search'], PDO::PARAM_STR);
        }

        // Bind limit and offset as integers
        $stmnt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmnt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmnt->execute();
            return $stmnt->fetchAll(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e){
            return ["error"=>$e->getMessage()];
        }

    }



    public static function get($id){
        try{
            $pdo = Database::get_connection();
            // select specific user via user id
            $sql = "SELECT * FROM users WHERE user_id = ?";
            $stmnt = $pdo->prepare($sql);
            $stmnt->execute([$id]);
            return $stmnt->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e){
            return ["error"=>$e->getMessage()];
        }
    }

    public static function edit($user_name,$user_phone,$user_email,$id){
        try{
            $pdo = Database::get_connection();
            // update user details
            $sql = "UPDATE users SET user_name= ?,user_phone =?,user_email=? WHERE user_id = ?";
            $stmnt = $pdo->prepare($sql);
            $stmnt->execute([$user_name, $user_phone, $user_email, $id]);
            return ["success"=>"You have successfully edited User details"];
        }
        catch(PDOException $e){
            return ["error"=>$e->getMessage()];
        }
    }

    public static function add($user_name, $user_email, $user_phone, $user_password, $user_role, $user_class){
        try{
        $pdo = Database::get_connection();
        $sql = "INSERT INTO users(user_name, user_email, user_phone, user_password, user_role, user_class) VALUES (?,?,?,?,?,?)";
        $stmnt = $pdo->prepare($sql);
        $success = $stmnt->execute([$user_name, $user_email, $user_phone, $user_password, $user_role, $user_class]);
        if($success){
            return ["success"=>"User added successfully"];
        }
        }
        catch(PDOException $e){
            if($e->errorInfo[1] == 1062){
                return ["error"=>"User is already in database."];
            }
            return ["error"=>"Couldn't add new user". $e->getMessage()];
        }
        
    }

    public static function validate($data){
        $required = ["user_name","user_email","user_phone","user_password","user_role","user_class"];
        foreach($required as $field){
            if(empty($data[$field])){
                return ["error"=>"Please provide {$field}"];
            }
        }
        return TRUE;
    }

    public static function login($user_phone, $user_pw){
    try{
        $pdo = Database::get_connection();
        $sql = "SELECT * FROM users WHERE user_phone = ?";
        $stmnt = $pdo->prepare($sql);
        $stmnt->execute([$user_phone]);
        $user = $stmnt->fetch(PDO::FETCH_ASSOC);
        if(!$user){
            return ["error"=>"User not found"];
        }
        $hashed_pw = $user["user_password"];
        if(password_verify($user_pw, $hashed_pw)){
            return [$user["user_name"], $user["user_role"], $user["user_phone"]];
        }
        else{
            return ["error"=>"Wrong password"];
        }
    }
    catch(PDOException $e){
        return ["error"=>$e->getMessage()];
    }
}


}




?>