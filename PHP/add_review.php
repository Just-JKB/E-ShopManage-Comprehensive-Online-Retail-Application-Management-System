<?php
require_once 'dbConnection.php';
session_start();

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

// Read JSON input
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (
    empty($data['product_id']) ||
    empty($data['rating']) ||
    empty($data['comment'])
) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields']);
    exit;
}

$product_id = intval($data['product_id']);
$rating = intval($data['rating']);
$comment = trim($data['comment']);
$user_id = $_SESSION['user_id'];

try {
    $db = new Database();
    $conn = $db->getConnection();

    // Prepare the stored procedure
    $stmt = $conn->prepare("CALL InsertReview(:user_id, :product_id, :rating, :comment)");

    // Bind the parameters
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
    $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);

    // Execute the stored procedure
    $stmt->execute();

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}