<?php

class DatabaseConnection {
    private $db_host;
    private $db_name;
    private $db_user;
    private $db_pass;
    private $connection;

    // Constructor to initialize database parameters
    public function __construct($db_host, $db_name, $db_user, $db_pass) {
        $this->db_host = $db_host;
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_pass = $db_pass;
        $this->connection = null;
    }

    // Method to establish a database connection
    public function connect() {
        try {
            $dsn = "mysql:host=$this->db_host;dbname=$this->db_name";
            $this->connection = new PDO($dsn, $this->db_user, $this->db_pass);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo "Connected to database successfully.\n";
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage() . "\n";
        }
    }

    // Method to close the database connection
    public function close() {
        $this->connection = null;
        echo "Connection closed.\n";
    }
}

// Example usage:
$db = new DatabaseConnection("localhost", "example_db", "root", "password");
$db->connect();
// Perform operations...
$db->close();

?>
