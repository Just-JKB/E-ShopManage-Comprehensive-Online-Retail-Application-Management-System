<?php
require_once 'dbConnection.php';
require_once 'InsertOrder.php';

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("CALL GetAllOrdersWithDetails()");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Dashboard</title>
    <style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 20px;
    background-color: #f4f6f9;
    color: #333;
}

h1 {
    text-align: center;
    color: #2c3e50;
    margin-bottom: 40px;
}

.order {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 30px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
}

.order h2 {
    margin-top: 0;
    color: #34495e;
}

.order p {
    margin: 5px 0 15px;
    font-size: 0.95rem;
    color: #555;
}

.product-list {
    margin-top: 10px;
}

.product {
    display: flex;
    align-items: center;
    margin-top: 15px;
    padding: 10px;
    border-top: 1px solid #eee;
}

.product:first-child {
    border-top: none;
}

.product-details {
    flex-grow: 1;
}

.product-details strong {
    font-size: 1rem;
    color: #2c3e50;
}

.product-details div {
    font-size: 0.9rem;
    color: #666;
}

/* Responsive */
@media (max-width: 600px) {
    .product {
        flex-direction: column;
        align-items: flex-start;
    }
}

    </style>
</head>
<body>
    <h1>Order Dashboard</h1>
    <h1><a href="UserDashboard.php">
    <button style="padding: 10px 20px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer;">
        &larr; Back to Dashboard
    </button>
</a><h1>
    

    <?php if (count($orders) === 0): ?>
        <p>No orders found.</p>
    <?php else: ?>
        <?php 
        $currentOrder = null;
        foreach ($orders as $order): 
            if ($currentOrder !== $order['order_id']):
                if ($currentOrder !== null) echo "</div>"; // close previous
                $currentOrder = $order['order_id'];
        ?>
            <div class="order">
                <h2>Order #<?= $order['order_id'] ?></h2>
                <p><strong>Date:</strong> <?= $order['order_date'] ?> | <strong>Status:</strong> <?= $order['status'] ?> | <strong>Total:</strong> ₱<?= number_format($order['total_price'], 2) ?></p>
                <div class="product-list">
        <?php endif; ?>
            <div class="product">
                <div class="product-details">
                    <div><strong><?= htmlspecialchars($order['product_name']) ?></strong></div>
                    <div>Quantity: <?= $order['quantity'] ?> | Price: ₱<?= number_format($order['price'], 2) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>

</body>
</html>
