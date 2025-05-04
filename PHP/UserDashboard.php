<?php
require_once 'dbConnection.php';

// Create database connection
$db = new Database();
$conn = $db->getConnection();

// Get filter values from GET parameters
$categoryId = !empty($_GET['category_id']) ? $_GET['category_id'] : null;
$size = !empty($_GET['size']) ? $_GET['size'] : null;
$minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? $_GET['min_price'] : null;
$maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? $_GET['max_price'] : null;

// Fetch filtered products using stored procedure
$stmt = $conn->prepare("CALL FilterProducts(:category_id, :size, :min_price, :max_price)");
$stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
$stmt->bindParam(':size', $size, PDO::PARAM_STR);
$stmt->bindParam(':min_price', $minPrice);
$stmt->bindParam(':max_price', $maxPrice);

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();
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

        form {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        select, input[type="number"] {
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ccc;
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

<!-- Filter Form -->
<form method="GET">
    <select name="category_id">
        <option value="">All Categories</option>
        <?php
        // Fetch categories for the filter
        $catStmt = $db->getConnection()->query("SELECT * FROM categories");
        while ($cat = $catStmt->fetch(PDO::FETCH_ASSOC)) {
            $selected = (isset($_GET['category_id']) && $_GET['category_id'] == $cat['category_id']) ? 'selected' : '';
            echo "<option value='{$cat['category_id']}' $selected>{$cat['category_name']}</option>";
        }
        ?>
    </select>

    <select name="size">
        <option value="">All Sizes</option>
        <option value="S" <?= ($_GET['size'] ?? '') == 'S' ? 'selected' : '' ?>>S</option>
        <option value="M" <?= ($_GET['size'] ?? '') == 'M' ? 'selected' : '' ?>>M</option>
        <option value="L" <?= ($_GET['size'] ?? '') == 'L' ? 'selected' : '' ?>>L</option>
    </select>

    <input type="number" name="min_price" placeholder="Min Price" step="0.01" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
    <input type="number" name="max_price" placeholder="Max Price" step="0.01" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">

    <button type="submit" class="order-button">Apply Filter</button>
</form>

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
