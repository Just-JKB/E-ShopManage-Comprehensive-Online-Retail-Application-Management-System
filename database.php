<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "e-shopmanage"; // Replace with your database name

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully using PDO!";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>