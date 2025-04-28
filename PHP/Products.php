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
        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .product-details {
            padding: 15px;
        }
        .product-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .product-price {
            color: #888;
            font-size: 16px;
        }
    </style>
</head>
<body>

<div class="header">
    <h1>User Dashboard</h1>
    <a href="OrderDashboard.php" class="order-button">Orders</a>
</div>

<div class="product-grid">
    <?php foreach ($products as $product): ?>
        <?php 
            $image = !empty($product['image_url']) ? htmlspecialchars($product['image_url']) : 'images/default.png';
        ?>
        <div class="product-card">
            <img src="<?php echo $image; ?>" alt="Product Image" class="product-image">
            <div class="product-details">
                <div class="product-name"><?php echo htmlspecialchars($product['product_name']); ?></div>
                <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                <!-- Order button added inside the product card -->
                <button class="order-button" onclick="addToCart(<?php echo $product['product_id']; ?>, '<?php echo addslashes($product['product_name']); ?>', <?php echo $product['price']; ?>)">Order</button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<script>
    // JavaScript function to add product to cart
    let cart = [];

    function addToCart(productId, productName, price) {
        const product = {
            productId: productId,
            productName: productName,
            price: price,
            quantity: 1 // Initially add 1 product
        };

        // Check if the product already exists in the cart
        const existingProduct = cart.find(item => item.productId === productId);

        if (existingProduct) {
            // If product exists, increase quantity
            existingProduct.quantity += 1;
        } else {
            // Add new product to the cart
            cart.push(product);
        }

        // Show success message
        alert(`${productName} has been added to your cart!`);
    }
</script>

</body>
</html>