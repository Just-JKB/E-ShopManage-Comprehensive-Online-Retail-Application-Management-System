<?php
require_once 'dbConnection.php';
require_once 'InsertOrder.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    $user_id = $data['user_id'];
    $total_price = $data['total_price'];
    $order_date = date('Y-m-d');
    $status = 'Pending';
    $order_items = json_encode($data['order_items']);

    $db = new Database();
    $conn = $db->getConnection();

    $stmt = $conn->prepare("CALL InsertOrder(:p_user_id, :p_total_price, :p_order_date, :p_status, :p_order_items)");
    $stmt->execute([
        ':p_user_id' => $user_id,
        ':p_total_price' => $total_price,
        ':p_order_date' => $order_date,
        ':p_status' => $status,
        ':p_order_items' => $order_items
    ]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode([
        'success' => true,
        'order_id' => $result['order_id'] ?? null
    ]);

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
