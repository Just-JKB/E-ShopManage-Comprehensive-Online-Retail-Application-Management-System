<?php
require_once 'dbConnection.php';

// Set headers to allow any origin and content type
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Log the request for debugging
error_log('Delete request received: ' . print_r($_POST, true));

// Check if product_id is provided
if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'No product ID provided.'
    ]);
    exit;
}

$product_id = (int) $_POST['product_id'];

try {
    // Create database connection
    $db = new Database();
    $conn = $db->getConnection();

    if (!$conn) {
        throw new Exception("Database connection failed");
    }

    // Prepare and execute delete statement
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->execute([$product_id]);

    // Check if any rows were affected
    if ($stmt->rowCount() > 0) {
        echo json_encode([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Product not found or already deleted.'
        ]);
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database Error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}