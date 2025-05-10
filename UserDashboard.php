<?php
require_once 'dbConnection.php';
session_start();

$db = new Database();
$conn = $db->getConnection();

$productStmt = $conn->query("SELECT * FROM products");
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

$conn = null;
?>

<!-- ...HTML HEAD... -->
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

<!-- Review Modal -->
<div id="reviewModal">
    <div class="modal-content">
        <h3 id="reviewProductTitle">Product Reviews</h3>
        <div id="reviewList">Loading reviews...</div>

        <hr>

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
    document.getElementById("reviewModal").style.display = "flex";
    document.getElementById("reviewList").innerHTML = "Loading reviews...";
    document.getElementById("reviewProductId").value = productId;

    fetch(`fetch_reviews.php?product_id=${productId}`)
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById("reviewList");
            if (data.length === 0) {
                list.innerHTML = "<p>No reviews yet.</p>";
            } else {
                list.innerHTML = data.map(r => `
                    <div style="border-bottom: 1px solid #ccc; margin-bottom: 10px; padding-bottom: 8px;">
                        <strong>Rating:</strong> ${'⭐'.repeat(r.rating)}<br>
                        <strong>Comment:</strong> ${r.comment}<br>
                        <em>By User #${r.user_id}</em>
                    </div>
                `).join('');
            }
        })
        .catch(() => {
            document.getElementById("reviewList").innerHTML = "<p>Error loading reviews.</p>";
        });
}

function closeReviewModal() {
    document.getElementById("reviewModal").style.display = "none";
}

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
            showReviews(productId);
            document.getElementById("reviewForm").reset();
        } else {
            Swal.fire("Error", data.error || "Could not add review.", "error");
        }
    })
    .catch(() => {
        Swal.fire("Error", "Request failed.", "error");
    });
});
</script>
