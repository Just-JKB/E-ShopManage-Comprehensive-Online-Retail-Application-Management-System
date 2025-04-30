<?php
require_once 'dbConnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get all POST values
    $user_id      = $_POST['userId'] ?? '';
    $total_price  = $_POST['totalPrice'] ?? '';
    $order_date   = $_POST['orderDate'] ?? '';
    $status       = $_POST['status'] ?? '';
    $order_items  = $_POST['orderItems'] ?? '[]'; // Expecting JSON string

    // Call the insert function
    $result = InsertOrderWithItems($user_id, $total_price, $order_date, $status, $order_items);

    // Set JSON header and return clean response
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

function InsertOrderWithItems($user_id, $total_price, $order_date, $status, $order_items_json) {
    try {
        $db = new Database();
        $conn = $db->getConnection();

        if ($conn === null) {
            throw new Exception("Database connection failed.");
        }

        $stmt = $conn->prepare("CALL InsertOrderWithItems(:p_user_id, :p_total_price, :p_order_date, :p_status, :p_order_items)");
        $stmt->execute([
            ':p_user_id'     => $user_id,
            ':p_total_price' => $total_price,
            ':p_order_date'  => $order_date,
            ':p_status'      => $status,
            ':p_order_items' => $order_items_json
        ]);

        // Fetch the order_id from the stored procedure result
        $orderData = $stmt->fetch(PDO::FETCH_ASSOC);
        $order_id = $orderData['order_id'] ?? null;

        if (!$order_id) {
            throw new Exception("Failed to retrieve inserted order ID.");
        }

        // Fetch inserted order
        $orderStmt = $conn->prepare("SELECT * FROM orders WHERE order_id = :order_id");
        $orderStmt->execute([':order_id' => $order_id]);
        $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'order' => $order,
            'message' => '✅ Order and items inserted successfully.'
        ];

    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => '❌ Database Error: ' . $e->getMessage()
        ];
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => '❌ Error: ' . $e->getMessage()
        ];
    }
}
?>
