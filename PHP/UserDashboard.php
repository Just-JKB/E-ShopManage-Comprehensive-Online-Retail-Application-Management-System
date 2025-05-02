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

        .order-button {
            padding: 10px 20px;
            background: #4CAF50;
            color: white;
            border: none;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        .order-button:hover {
            background: #45a049;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .product-card {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            transition: 0.3s;
        }

        .product-card:hover {
            box-shadow: 0 5px 10px rgba(0,0,0,0.2);
        }

        .product-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .product-details {
            padding: 15px;
        }

        .product-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }

        .product-price {
            color: #888;
            font-size: 16px;
            text-align: center;
            margin-bottom: 10px;
        }
        .profile-header {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #fff;
    padding: 8px 12px;
    border-radius: 20px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
}

.profile-name {
    font-weight: bold;
    font-size: 14px;
    color: #333;
}

.profile-img {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
}
    </style>
</head>
<body>

<div class="header">
    <h1>User Dashboard</h1>
    <div style="display: flex; align-items: center; gap: 15px;">
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
                alt="<?= htmlspecialchars($product['product_name']) ?>">
            <div class="product-details">
                <div class="product-name"><?= htmlspecialchars($product['product_name']) ?></div>
                <div class="product-price">₱<?= number_format($product['price'], 2) ?></div>
                <button class="order-button" onclick="orderNow(<?= $product['product_id'] ?>, '<?= addslashes($product['product_name']) ?>', <?= $product['price'] ?>)">Order</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    function orderNow(productId, productName, price) {
        const orderData = {
            user_id: 1, // Replace with session user ID if needed
            total_price: price,
            order_items: [
                {
                    product_id: productId,
                    quantity: 1,
                    price: price
                }
            ]
        };

        fetch('PlaceOrder.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(orderData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(`${productName} ordered successfully! Order ID: ${data.order_id}`);
                window.location.href = 'OrderDashboard.php';
            } else {
                alert('Order failed: ' + data.error);
            }
        })
        .catch(error => {
            alert('Order error: ' + error);
        });
    }
</script>

</body>
</html>
