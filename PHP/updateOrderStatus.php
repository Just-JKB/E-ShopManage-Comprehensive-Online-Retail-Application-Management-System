<?php
require_once 'dbConnection.php';
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $orderId = $_POST['order_id'];
    $status = $_POST['status'];

    try {
        $stmt = $conn->prepare("UPDATE orders SET status = :status WHERE order_id = :order_id");
        $stmt->execute(['status' => $status, 'order_id' => $orderId]);
        echo "Order status updated.";
    } catch (PDOException $e) {
        echo "Update failed: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
