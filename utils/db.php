<?php

class Database {
    private static $pdo = NULL;

    private $db_host;
    private $db_name;
    private $db_user;

    private function __construct() {
        // ✅ Assign runtime values here
        $this->db_host = getenv("DB_HOST");
        $this->db_name = getenv("DB_NAME");
        $this->db_user = getenv("DB_USER");

        if (empty(self::$pdo)) {
            try {
                $dsn = "mysql:host={$this->db_host};dbname={$this->db_name};charset=utf8mb4";
                self::$pdo = new PDO($dsn, $this->db_user);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Database connection failed: " . $e->getMessage());
            }
        }
    }

    public static function get_connection() {
        if (self::$pdo === NULL) {
            new Database();
        }
        return self::$pdo;
    }
}
?>