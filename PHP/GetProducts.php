<?php
require_once 'dbConnection.php';

// Get all products from DB
function getProducts() {
    try {
        $db = new Database();
        $conn = $db->getConnection();

        if ($conn === null) {
            throw new Exception("Database connection failed.");
        }

        // Retrieve all products
        $stmt = $conn->prepare("SELECT * FROM products"); // Change to match your DB
        $stmt->execute();

        // Fetch all products
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $products;

    } catch (PDOException $e) {
        return [];
    } catch (Exception $e) {
        return [];
    }
}

// Send the response
header('Content-Type: application/json');
echo json_encode(getProducts());
?>
