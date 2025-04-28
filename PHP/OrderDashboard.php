<?php
require_once 'dbConnection.php';

// Create database connection
$db = new Database();
$conn = $db->getConnection();

// Get filter from URL, default is 'All'
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'All';

// Prepare SQL query with status filter
if ($status_filter === 'All') {
    $orderStmt = $conn->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY order_date DESC");
} else {
    $orderStmt = $conn->prepare("SELECT * FROM orders WHERE user_id = :user_id AND status = :status ORDER BY order_date DESC");
    $orderStmt->bindParam(':status', $status_filter);
}

$orderStmt->bindParam(':user_id', $user_id);
$orderStmt->execute();
$orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

// Close connection
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Dashboard</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .back-button {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
        }
        .back-button:hover {
            background: #45a049;
        }
        .order-list {
            margin-top: 30px;
        }
        .order-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: 0.3s;
            margin-bottom: 20px;
            padding: 15px;
        }
        .order-card:hover {
            box-shadow: 0 5px 10px rgba(0,0,0,0.2);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
        }
        .order-id {
            font-weight: bold;
        }
        .order-details {
            font-size: 16px;
            margin-top: 10px;
        }
        .order-price {
            font-weight: bold;
            color: #4CAF50;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>Your Orders</h1>
    <a href="Products.php" class="back-button">Back to Dashboard</a>
</div>

<!-- Status Filter Dropdown -->
<form action="OrderDashboard.php" method="GET">
    <label for="status">Filter by Status:</label>
    <select name="status" id="status">
        <option value="All" <?php echo $status_filter === 'All' ? 'selected' : ''; ?>>All</option>
        <option value="Pending" <?php echo $status_filter === 'Pending' ? 'selected' : ''; ?>>Pending</option>
        <option value="Shipped" <?php echo $status_filter === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
        <option value="Delivered" <?php echo $status_filter === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
        <option value="Cancelled" <?php echo $status_filter === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
    </select>
    <button type="submit">Filter</button>
</form>

<div class="order-list">
    <?php foreach ($orders as $order): ?>
        <div class="order-card">
            <div class="order-header">
                <div class="order-id">Order ID: <?php echo $order['order_id']; ?></div>
                <div class="order-status"><?php echo htmlspecialchars($order['status']); ?></div>
            </div>
            <div class="order-details">
                <div><strong>Date:</strong> <?php echo date('Y-m-d H:i:s', strtotime($order['order_date'])); ?></div>
                <div class="order-price"><strong>Total:</strong> $<?php echo number_format($order['total_price'], 2); ?></div>
            </div>
            
            <!-- Status Change Form -->
            <form action="OrderDashboard.php" method="POST">
                <input type="hidden" name="order_id" value="<?php echo $order['order_id']; ?>">
                <label for="status">Change Status:</label>
                <select name="status" id="status">
                    <option value="Pending" <?php echo $order['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="Shipped" <?php echo $order['status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                    <option value="Delivered" <?php echo $order['status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                    <option value="Cancelled" <?php echo $order['status'] === 'Cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                </select>
                <button type="submit">Update Status</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
