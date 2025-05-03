<?php
require_once 'dbConnection.php'; // Adjust the path if necessary

// Get the product ID from the POST request
$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['productId'];

if ($productId) {
    try {
        // Database connection
        $db = new Database();
        $conn = $db->getConnection();

        // Prepare and execute stored procedure to delete the product
        $stmt = $conn->prepare("CALL deleteProduct(:productId)");
        $stmt->bindParam(':productId', $productId, PDO::PARAM_INT);
        $stmt->execute();

        // Return a success response
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        // Return an error response if something goes wrong
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}
?>
