<?php
require_once 'dbConnection.php';

// Create database connection
$db = new Database();
$conn = $db->getConnection();

// Fetch products
$productStmt = $conn->query("SELECT * FROM products");
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

// Close connection
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../CSS/UserDashboard.css">
    <script src="../JS/order.js"></script>
</head>
<body>

<div class="header">
    <h1>User Dashboard</h1>
    <div style="display: flex; align-items: center; gap: 15px;">
        <input type="text" id="searchInput" placeholder="Search products...">
        <button id="searchButton" class="order-button">Search</button>
        <a href="OrderDashboard.php" class="order-button">Order-List</a>
        <a href="UserProfile.php" class="profile-link">
            <div class="profile-header">
                <img src="images/profile.png" alt="Profile" class="profile-img">
            </div>
        </a>
    </div>
</div>

<div class="product-grid">
    <?php foreach ($products as $product): ?>
        <div class="product-card" data-product-id="<?= $product['product_id'] ?>">
            <img src="<?= '../' . htmlspecialchars($product['image_url'] ?? 'images/default-product.jpg') ?>" 
                 alt="<?= htmlspecialchars($product['product_name']) ?>"
                 style="cursor:pointer;"
                 onclick="openReviewModal(<?= $product['product_id'] ?>)">
            <div class="product-details">
                <div class="product-name"><?= htmlspecialchars($product['product_name']) ?></div>
                <div class="product-price">₱<?= number_format($product['price'], 2) ?></div>
                <button class="order-button" onclick="orderNow(<?= $product['product_id'] ?>, '<?= addslashes($product['product_name']) ?>', <?= $product['price'] ?>)">Order</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal for Order -->
<div id="orderModal">
    <div class="modal-content">
        <h3>Place Your Order</h3>
        <form id="orderForm">
            <input type="hidden" id="modalProductId">
            <input type="hidden" id="modalProductName">
            <input type="hidden" id="modalProductPrice">

            <label>Quantity:</label>
            <input type="number" id="quantity" min="1" value="1" required class="order-input"><br><br>

            <label>Payment Method:</label>
            <select id="paymentMethod" required class="order-input">
                <option value="">-- Select Payment --</option>
                <option value="Cash on Delivery">Cash on Delivery</option>
                <option value="GCash">GCash</option>
                <option value="Credit Card">Credit Card</option>
            </select><br><br>

            <button type="submit" class="order-button">Confirm Order</button>
            <button type="button" onclick="closeModal()" class="order-button" style="background:#ccc; color:#333;">Cancel</button>
        </form>
    </div>
</div>

<!-- Modal for Reviews -->
<div id="reviewModal">
    <div class="modal-content">
        <h3>Product Reviews</h3>
        <div id="reviewList"></div>
        <button onclick="closeReviewModal()" class="order-button" style="background:#ccc; color:#333;">Close</button>
    </div>
</div>

</body>
</html>