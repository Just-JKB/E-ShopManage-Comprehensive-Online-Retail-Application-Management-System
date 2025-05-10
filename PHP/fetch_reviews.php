<?php
require_once 'dbConnection.php';

if (isset($_GET['product_id'])) {
    $productId = $_GET['product_id'];

    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("SELECT rating, comment, user_id FROM product_reviews WHERE product_id = ?");
    $stmt->execute([$productId]);
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($reviews);
} else {
    echo json_encode([]);
}
