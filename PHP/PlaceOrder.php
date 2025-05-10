<?php
require_once 'dbConnection.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data)) {
        throw new Exception("No JSON received or invalid format.");
    }

    if (empty($data['user_id']) || empty($data['order_details'])) {
        throw new Exception("Missing required fields: user_id or order_details");
    }



    $db = new Database();
    $conn = $db->getConnection();

    // Start transaction
    $conn->beginTransaction();

    // 1. Create the order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, order_date, status) 
                           VALUES (?, ?, NOW(), 'Pending')");
    $stmt->execute([
        $data['user_id'],
        $data['total_price']
    ]);
    
    $order_id = $conn->lastInsertId();

    // 2. Process order details and update inventory
    foreach ($data['order_details'] as $detail) {
        // Insert order detail
        $detail_stmt = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, subtotal) 
                                      VALUES (?, ?, ?, ?)");
        $detail_stmt->execute([
            $order_id,
            $detail['product_id'],
            $detail['quantity'],
            $detail['subtotal']
        ]);

        // Update inventory (using stock_quantity)
        $update_stmt = $conn->prepare("UPDATE products 
                                     SET stock_quantity = stock_quantity - ? 
                                     WHERE product_id = ? AND stock_quantity >= ?");
        $update_stmt->execute([
            $detail['quantity'],
            $detail['product_id'],
            $detail['quantity']
        ]);

        if ($update_stmt->rowCount() === 0) {
            throw new Exception("Insufficient stock for product ID: " . $detail['product_id']);
        }
    }

    // Commit transaction
    $conn->commit();

    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'message' => 'Order placed successfully'
    ]);

} catch (PDOException $e) {
    if (isset($conn)) $conn->rollBack();
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    if (isset($conn)) $conn->rollBack();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}