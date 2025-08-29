<?php 
require_once(__DIR__."/../utils/db.php");

class User{
    public function all($page,$search){
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
                session_start();
                $_SESSION["user_phone"] = $user["user_phone"];
                $_SESSION["user_role"] = $user["user_role"];
                return ["success"=> "Welcome {$user["user_name"]}"];
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