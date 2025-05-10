<?php
require_once 'dbConnection.php';

if (!isset($_GET['product_id'])) {
    http_response_code(400);
    echo json_encode(["error" => "Missing product_id"]);
    exit;
}

$product_id = $_GET['product_id'];

try {
    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT user_id, rating, comment FROM reviews WHERE product_id = :product_id ORDER BY review_id DESC");
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();

    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($reviews);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error"]);
}
?>
