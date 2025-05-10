<?php
require_once 'dbConnection.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $db = new Database();
    $conn = $db->getConnection();

    $userId = $_POST['user_id'];
    $productId = $_POST['product_id'];
    $orderId = $_POST['order_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("INSERT INTO product_reviews (user_id, product_id, order_id, rating, comment) 
                            VALUES (:user_id, :product_id, :order_id, :rating, :comment)");
    $stmt->execute([
        ':user_id' => $userId,
        ':product_id' => $productId,
        ':order_id' => $orderId,
        ':rating' => $rating,
        ':comment' => $comment
    ]);

    echo "Review submitted successfully!";
}
