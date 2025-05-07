<?php
require_once 'dbConnection.php';
session_start();

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
