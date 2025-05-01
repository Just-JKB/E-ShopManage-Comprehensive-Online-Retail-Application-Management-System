<?php
require_once 'dbConnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST data
    $user_id        = $_POST['user_id'] ?? null;
    $total_price    = $_POST['total_price'] ?? 0;
    $order_date     = $_POST['order_date'] ?? date('Y-m-d');
    $status         = $_POST['status'] ?? 'Pending';
    $order_items    = $_POST['order_items'] ?? '[]'; // JSON array

    // Call insert function
    $result = insertOrder($user_id, $total_price, $order_date, $status, $order_items);

    // Return response
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

function insertOrder($user_id, $total_price, $order_date, $status, $order_items) {
    try {
        $db = new Database();
        $conn = $db->getConnection();

        if (!$conn) {
            throw new Exception("Failed to connect to database.");
        }

        // Prepare and call the InsertOrder procedure
        $stmt = $conn->prepare("CALL InsertOrder(:p_user_id, :p_total_price, :p_order_date, :p_status, :p_order_items)");
        $stmt->execute([
            ':p_user_id'     => $user_id,
            ':p_total_price' => $total_price,
            ':p_order_date'  => $order_date,
            ':p_status'      => $status,
            ':p_order_items' => $order_items
        ]);

        // Fetch result (new order ID)
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'success' => true,
            'message' => '✅ Order placed successfully.',
            'order_id' => $result['order_id'] ?? null
        ];
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => '❌ Database Error: ' . $e->getMessage()
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => '❌ Error: ' . $e->getMessage()
        ];
    }
}
