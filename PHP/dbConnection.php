<?php
class Database {
    private $host = "localhost";
    private $db_name = "e-shopmanage"; 
    private $username = "root"; 
    private $password = ""; 
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            // Detailed logging for error
            error_log("Connection failed: " . $exception->getMessage(), 3, "/var/log/php_errors.log");
            throw new Exception("Connection error: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
