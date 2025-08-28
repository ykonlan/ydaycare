<?php 
// importing the database file
require_once "../utils/db.php";

// Ward model
class Ward{
    public static function all($page, $search){
        try{
            // select wards
            $pdo = Database::get_connection();
            if(!$search){
                $sql = "SELECT * FROM wards ORDER BY ward_name LIMIT ? OFFSET ?";
                $stmnt = $pdo->prepare($sql);
                $stmnt->execute([20, (($page-1) * 20)]);
            }
            else{
                $sql="SELECT * FROM wards WHERE ward_name ILIKE ? or ward_class ILIKE ? ORDER BY ward_name LIMIT ? OFFSET ? " ;
                $stmnt = $pdo->prepare($sql);
                $stmnt->execute([$search, $search, 20, (($page-1) * 20)]);
            }
            $wards = $result->fetchAll(PDO::FETCH_ASSOC);
            return ($wards);
        }
        catch(PDOException $e){
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

    public static function edit($ward_name, $ward_parent, $ward_class, $id){
        try{
            $pdo = Database::get_connection();
            // update ward details
            $sql = "UPDATE wards SET ward_name= ?,ward_parent =?,ward_class=? WHERE ward_id = ?";
            $stmnt = $pdo->prepare($sql);
            $stmnt->execute([$ward_name, $ward_parent, $ward_class, $id]);
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
                $pdo->rollback();
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
            $pdo->rollback();
            return ["error"=>$e->getMessage()];
        }
    }
    }
?>