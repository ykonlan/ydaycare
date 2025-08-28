<?php

// custom Database class
class Database{
    // static pdo for the whole class
    private static $pdo = NULL;

    // object attributes from env
    private $db_host = getenv("DB_HOST");
    private $db_name = getenv("DB_NAME");
    private $db_user = getenv("DB_USER");
    private $db_password = getenv("DB_PASSWORD");

    // constructor made private so Database objects cannot be created outside the class
    private function __construct(){
        if(empty(self::$pdo)){
            try{
            // establishing pdo connection

            $dsn = "mysql:host={$this->db_host};dbname={$this->db_name};charset=utf8mb4";
            self::$pdo = new PDO($dsn,$this->db_user,$this->db_password);

            // enabling error and exception catching mode
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);}
            catch(PDOException $e){
                die("Database connection failed". $e->getMessage());
            }
        }}

    // wrapper (public callable function to return pdo object)
    public static function get_connection(){
        if(self::$pdo === NULL){
            new Database();
        }
        return self::$pdo;
    }
}

?>