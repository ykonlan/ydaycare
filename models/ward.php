<?php 
// importing the database file
require_once (__DIR__."/../utils/db.php");

// Ward model
class Ward{
    public static function all($page, $search){
        try{
            $pdo = Database::get_connection();
            $limit = 20;
            $offset = ($page - 1) * $limit;

            if(!$search){
                $sql = "SELECT * FROM wards ORDER BY ward_name LIMIT :limit OFFSET :offset";
                $stmnt = $pdo->prepare($sql);
                $stmnt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmnt->bindValue(':offset', $offset, PDO::PARAM_INT);
            } else {
                // case-insensitive search for ward name or class
                $sql = "SELECT * FROM wards 
                        WHERE LOWER(ward_name) LIKE :search 
                        OR LOWER(ward_class) LIKE :search
                        ORDER BY ward_name 
                        LIMIT :limit OFFSET :offset";
                $stmnt = $pdo->prepare($sql);
                $stmnt->bindValue(':search', "%".strtolower(trim($search))."%", PDO::PARAM_STR);
                $stmnt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmnt->bindValue(':offset', $offset, PDO::PARAM_INT);
            }

            $stmnt->execute();
            $wards = $stmnt->fetchAll(PDO::FETCH_ASSOC);
            return $wards;

        } catch(PDOException $e){
            return ["error"=>$e->getMessage()];
        }


    }

    public static function get($id){
        try{
            $pdo = Database::get_connection();
            // select specific ward via ward id
            $sql = "SELECT * FROM wards WHERE ward_id = ?";
            $stmnt = $pdo->prepare($sql);
            $stmnt->execute([$id]);
            return $stmnt->fetch(PDO::FETCH_ASSOC);
        }
        catch(PDOException $e){
            return ["error"=>$e->getMessage()];
        }
    }

    public static function edit($ward_name, $ward_parent, $ward_dob, $ward_class, $id){
        try{
            $pdo = Database::get_connection();
            // update ward details
            $sql = "UPDATE wards SET ward_name= ?,ward_parent =?,ward_class=? WHERE ward_id = ?";
            $stmnt = $pdo->prepare($sql);
            $stmnt->execute([$ward_name, $ward_parent, $ward_class, $id]);
            if($stmnt->rowCount() <= 0){
                return ["error"=>"No Ward was updated"];
            }
            return ["success"=>"You have successfully edited your ward details"];
        }
        catch(PDOException $e){
            return ["error"=>$e->getMessage()];
        }
    }

    public static function delete($id){
        try{
            $pdo = Database::get_connection();
            // open a transaction for deleting ward and discounting him from his class
            $pdo->beginTransaction();
            // selecting the ward's class and the population of that class
            $sql = "SELECT ward_class, class_population FROM wards INNER JOIN classes ON wards.ward_class = classes.class_name WHERE wards.ward_id = ?";
            $stmnt = $pdo->prepare($sql);
            $stmnt->execute([$id]);
            $row = $stmnt->fetch(PDO::FETCH_ASSOC);
            $ward_class = $row["ward_class"] ?? NULL;
            $pop = $row["class_population"] ?? NULL;
            // if no ward was obtained from query, ward does not exist, hence, no deletion was made so rollback transaction(don't reduce class population)
            if(!$ward_class){
                $pdo->rollBack();
                return ["error"=>"No such Ward found"];
            }
            // otherwise, proceed to delete ward and reduce class population
            $sql = "DELETE FROM wards WHERE ward_id = ?";
            $stmnt = $pdo->prepare($sql);
            $stmnt->execute([$id]);
            $sql = "UPDATE classes SET class_population = ? WHERE class_name = ?";
            $stmnt = $pdo->prepare($sql);
            $stmnt->execute([$pop - 1, $ward_class]);
            //close the transaction
            $pdo->commit();
            return ["success"=>"You have successfully deleted your ward details"];
        }
        catch(PDOException $e){
            $pdo->rollBack();
            return ["error"=>$e->getMessage()];
        }
    }

    // calling the generic form renderer to render form for ward creation if request is get
    public static function render_form(){
        return form_renderer("wards");
    }

    // adding ward when post data is submitted
    public static function add($ward_name, $ward_parent, $ward_dob, $ward_class){
        try{
        $pdo = Database::get_connection();
        $pdo->beginTransaction();
        $sql = "INSERT INTO wards(ward_name, ward_dob, ward_parent, ward_class) VALUES (?,?,?,?)";
        $stmnt = $pdo->prepare($sql);
        $success = $stmnt->execute([$ward_name, $ward_dob, $ward_parent, $ward_class]);
        if(!$success){
            $pdo->rollBack();
            return ["error"=>"Couldn't add new ward"];
        }
        $sql = "SELECT class_population FROM classes WHERE class_name = ?";
        $stmnt = $pdo->prepare($sql);
        $success = $stmnt->execute([$ward_class]);
        if(!$success){
            $pdo->rollBack();
            return ["error"=>"Couldn't add new ward"];
        }
        $class_population = $stmnt->fetch(PDO::FETCH_ASSOC)["class_population"];
        $sql = "UPDATE classes SET class_population=? WHERE class_name=?";
        $stmnt = $pdo->prepare($sql);
        $success = $stmnt->execute([$class_population+1, $ward_class]);
        if(!$success){
            $pdo->rollBack();
            return ["error"=>"Couldn't add new ward"];
        }
        $pdo->commit();
        return ["success"=> "Ward added successfully"];
        }
        catch(PDOException $e){
            $pdo->rollBack();
            if($e->errorInfo[1] == 1062){
                return ["error"=>"Ward is already in database."];
            }
            return ["error"=>"Couldn't add new ward". $e->getMessage()];
        }
        
    }

    // make sure all fields are populated
    public static function validate($data){
        $required = ["ward_name","ward_parent","ward_dob","ward_class"];
        foreach($required as $field){
            if(empty($data[$field])){
                return ["error"=>"Please provide {$field}"];
            }
        }
        return TRUE;
    }

}
?>