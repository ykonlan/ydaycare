<?php 
require_once "../utils/db.php"

class User{
    public function all(){
        try{
            $pdo = Database::get_connection();
            $result = $pdo->query("SELECT * FROM users");
            $users = $result->fetchAll(PDO::FETCH_ASSOC);
            return json_encode($users);
        }
        catch(PDOException $e){
            return json_encode(["error"=>$e->getMessage()]);
        }

    }
}

?>