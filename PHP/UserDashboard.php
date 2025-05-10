<?php
require_once 'dbConnection.php';
session_start();

// Create database connection
$db = new Database();
$conn = $db->getConnection();

// Get filter values from GET parameters
$categoryId = !empty($_GET['category_id']) ? $_GET['category_id'] : null;
$size = !empty($_GET['size']) ? $_GET['size'] : null;
$minPrice = isset($_GET['min_price']) && $_GET['min_price'] !== '' ? $_GET['min_price'] : null;
$maxPrice = isset($_GET['max_price']) && $_GET['max_price'] !== '' ? $_GET['max_price'] : null;
$search = !empty($_GET['search']) ? $_GET['search'] : null;

if (!empty($search)) {
    // Call SearchProducts procedure if search term is provided.
    // Note: We assume the procedure accepts:
    // p_product_name, p_category_id, p_size, p_color, p_min_price, p_max_price
    // Since no color filter is included in the form, we'll pass NULL for p_color.
    $stmt = $conn->prepare("CALL SearchProducts(:p_product_name, :p_category_id, :p_size, :p_color, :p_min_price, :p_max_price)");
    $stmt->bindParam(':p_product_name', $search, PDO::PARAM_STR);
    $stmt->bindParam(':p_category_id', $categoryId, PDO::PARAM_INT);
    $stmt->bindParam(':p_size', $size, PDO::PARAM_STR);
    $p_color = null; // No color input from the filter form
    $stmt->bindParam(':p_color', $p_color, PDO::PARAM_STR);
    $stmt->bindParam(':p_min_price', $minPrice);
    $stmt->bindParam(':p_max_price', $maxPrice);
} else {
    // If no search term, use the FilterProducts procedure.
    $stmt = $conn->prepare("CALL FilterProducts(:category_id, :size, :min_price, :max_price)");
    $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
    $stmt->bindParam(':size', $size, PDO::PARAM_STR);
    $stmt->bindParam(':min_price', $minPrice);
    $stmt->bindParam(':max_price', $maxPrice);
}

$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->closeCursor();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="../CSS/UserDashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        #reviewModal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 12px;
            width: 400px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0,0,0,0.2);
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

        select, input[type="number"], input[type="text"] {
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


<!-- Filter & Search Form -->
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
        <!-- Add other sizes as needed -->
    </select>

    <input type="number" name="min_price" placeholder="Min Price" step="0.01" value="<?= htmlspecialchars($_GET['min_price'] ?? '') ?>">
    <input type="number" name="max_price" placeholder="Max Price" step="0.01" value="<?= htmlspecialchars($_GET['max_price'] ?? '') ?>">
    
    <!-- Search Bar -->
    <input type="text" name="search" placeholder="Search product name..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

    <button type="submit" class="order-button">Apply Filter</button>
</form>

<div class="product-grid">
    <?php foreach ($products as $product): ?>
        <div class="product-card" onclick="showReviews(<?= $product['product_id'] ?>)">
            <img src="<?= '../' . htmlspecialchars($product['image_url'] ?? 'images/default-product.jpg') ?>" 
                 alt="<?= htmlspecialchars($product['product_name']) ?>">
            <div class="product-details">
                <div class="product-name"><?= htmlspecialchars($product['product_name']) ?></div>
                <div class="product-price">₱<?= number_format($product['price'], 2) ?></div>
                <button class="order-button" onclick="event.stopPropagation(); orderNow(<?= $product['product_id'] ?>, '<?= addslashes($product['product_name']) ?>', <?= $product['price'] ?>)">Order</button>
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
        <h3 id="reviewProductTitle">Product Reviews</h3>
        <div id="reviewList">Loading reviews...</div>

        <hr style="margin: 15px 0;">

        <h4>Add a Review</h4>
        <form id="reviewForm">
            <input type="hidden" id="reviewProductId">
            <label>Rating:</label>
            <select id="reviewRating" required>
                <option value="">--Select--</option>
                <option value="1">⭐</option>
                <option value="2">⭐⭐</option>
                <option value="3">⭐⭐⭐</option>
                <option value="4">⭐⭐⭐⭐</option>
                <option value="5">⭐⭐⭐⭐⭐</option>
            </select><br><br>

            <label>Comment:</label><br>
            <textarea id="reviewComment" rows="3" style="width: 100%;" required></textarea><br><br>

            <button type="submit" class="order-button">Submit Review</button>
        </form>

        <button class="order-button" onclick="closeReviewModal()" style="margin-top: 15px;">Close</button>
    </div>
</div>

<script>
function showReviews(productId) {
    const modal = document.getElementById("reviewModal");
    const list = document.getElementById("reviewList");
    const title = document.getElementById("reviewProductTitle");
    const reviewProductId = document.getElementById("reviewProductId");

    modal.style.display = "flex";
    list.innerHTML = "Loading reviews...";
    title.textContent = "Product Reviews";
    reviewProductId.value = productId;

    fetch(`fetch_reviews.php?product_id=${productId}`)
        .then(response => response.json())
        .then(data => {
            if (data.length === 0) {
                list.innerHTML = "<p>No reviews yet.</p>";
            } else {
                let html = "";
                data.forEach(review => {
                    html += `
                        <div style="border-bottom: 1px solid #ccc; margin-bottom: 10px; padding-bottom: 8px;">
                            <strong>Rating:</strong> ${'⭐'.repeat(review.rating)}<br>
                            <strong>Comment:</strong> ${review.comment}<br>
                            <em>By User #${review.user_id}</em>
                        </div>
                    `;
                });
                list.innerHTML = html;
            }
        })
        .catch(err => {
            list.innerHTML = "<p>Error loading reviews.</p>";
        });
}

function closeReviewModal() {
    document.getElementById("reviewModal").style.display = "none";
}

// Submit review
document.getElementById("reviewForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const productId = document.getElementById("reviewProductId").value;
    const rating = document.getElementById("reviewRating").value;
    const comment = document.getElementById("reviewComment").value;

    fetch("add_review.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ product_id: productId, rating, comment })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            Swal.fire("Success", "Review added!", "success");
            showReviews(productId); // Refresh review list
            document.getElementById("reviewForm").reset();
        } else {
            Swal.fire("Error", data.error || "Could not add review.", "error");
        }
    })
    .catch(err => {
        Swal.fire("Error", "Request failed.", "error");
    });
});
</script>

<script src="../JS/order.js"></script>
</body>
</html>
