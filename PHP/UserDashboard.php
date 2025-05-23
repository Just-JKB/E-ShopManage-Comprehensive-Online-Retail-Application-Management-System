<?php
session_start();
require_once 'dbConnection.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: userLogin.php");
    exit();
}

// Create database connection
$db = new Database();
$conn = $db->getConnection();

// Fetch products
$productStmt = $conn->query("SELECT * FROM products");
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch reviews grouped by product
$reviewStmt = $conn->query("SELECT product_id, rating, comment, user_id FROM product_reviews");
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);

// Organize reviews by product_id
$groupedReviews = [];
foreach ($reviews as $review) {
    $groupedReviews[$review['product_id']][] = $review;
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/UserDashboardd.css">
    <script src="../JS/order.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="sidebar-header mb-4">
                        <h3 class="text-light text-center">E-SHOP<br>MANAGE</h3>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="fas fa-home me-2"></i>
                                Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="OrderDashboard.php">
                                <i class="fas fa-shopping-bag me-2"></i>
                                My Orders
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="UserProfile.php">
                                <i class="fas fa-user me-2"></i>
                                My Profile
                            </a>
                        </li>
                        <li class="nav-item mt-5">
                            <a class="nav-link text-danger" href="../PHP/userLogout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Products</h1>
                    <div>
                        <a href="UserProfile.php">
                            <img src="images/profile.png" alt="Profile" class="rounded-circle border" width="40">
                        </a>
                    </div>
                </div>

                <div class="container-fluid p-0">
                    <div class="d-flex justify-content-center mb-4">
                        <div class="input-group" style="max-width: 500px;">
                            <input type="text" class="form-control" id="searchInput" placeholder="Search products...">
                            <button class="btn btn-primary" id="searchButton">
                                <i class="fas fa-search"></i> Search
                            </button>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <label class="me-2">Sort by:</label>
                            <select id="sortSelect" class="form-select d-inline-block w-auto">
                                <option value="name-asc">Name (A-Z)</option>
                                <option value="name-desc">Name (Z-A)</option>
                                <option value="price-asc">Price (Low to High)</option>
                                <option value="price-desc">Price (High to Low)</option>
                            </select>
                        </div>
                        <div>
                            <label class="me-2">Filter by Category:</label>
                            <select id="filterSelect" class="form-select d-inline-block w-auto">
                                <option value="all">All</option>
                                <option value="pants">PANTS</option>
                                <option value="t-shirts">T-SHIRTS</option>
                                <option value="underwear">UNDERWEAR</option>
                                <option value="blouses">BLOUSES</option>
                                <option value="dresses">DRESSES</option>
                            </select>
                        </div>
                        <div>
                            <a href="OrderDashboard.php" class="btn btn-success">
                                <i class="fas fa-list me-2"></i>Order List
                            </a>
                        </div>
                    </div>
                    
             <div class="container-fluid p-0">
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="productGrid">
        <?php foreach ($products as $product): ?>
            <div class="col product-card">
                <div class="card shadow-sm h-100">
                    <img src="<?= '../' . htmlspecialchars($product['image_url'] ?? 'images/default-product.jpg') ?>"
                         class="card-img-top"
                         alt="<?= htmlspecialchars($product['product_name']) ?>"
                         style="height: 250px; object-fit: cover; cursor:pointer;"
                         onclick="fetchReviews(<?= $product['product_id'] ?>)">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($product['product_name']) ?></h5>
                        <p class="card-text">₱<?= number_format($product['price'], 2) ?></p>
                        <button class="btn btn-primary w-100 mt-2"
                                onclick="orderNow(<?= $product['product_id'] ?>, '<?= addslashes($product['product_name']) ?>', <?= $product['price'] ?>)">
                            <i class="fas fa-shopping-cart me-2"></i>Order Now
                        </button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

            </main>
        </div>
    </div>

    <!-- Modal for Order -->
    <div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderModalLabel">Place Your Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="orderForm">
                        <input type="hidden" id="modalProductId">
                        <input type="hidden" id="modalProductName">
                        <input type="hidden" id="modalProductPrice">

                        <div class="mb-3">
                            <label for="quantity" class="form-label">Quantity:</label>
                            <input type="number" id="quantity" min="1" value="1" required class="form-control" oninput="updateSubtotal()">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subtotal:</label>
                            <p id="subtotalDisplay" class="form-control-plaintext">₱0.00</p>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="confirmOrder()">Confirm Order</button>
                </div>
            </div>
        </div>
    </div>

  <!-- Modal for displaying reviews -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Product Reviews</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="reviewContent">
                    <p class="text-center">Loading reviews...</p>
                </div>
            </div>
        </div>
    </div>
</div>


    

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Initialize Bootstrap modals
        var orderModal = new bootstrap.Modal(document.getElementById('orderModal'), {
            keyboard: false
        });
        
        var reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'), {
            keyboard: false
        });
        
        // Order Now function
       function orderNow(productId, productName, productPrice) {
            document.getElementById('modalProductId').value = productId;
            document.getElementById('modalProductName').value = productName;
            document.getElementById('modalProductPrice').value = productPrice;
            
            document.getElementById('quantity').value = 1;
            updateSubtotal();

            orderModal.show();
        }

        function fetchReviews(productId) {
        $('#reviewContent').html('<p class="text-center">Loading reviews...</p>');
        $('#reviewModal').modal('show');
        
        $.ajax({
            url: 'fetch_reviews.php',
            method: 'GET',
            data: { product_id: productId },
            success: function (response) {
                let reviews = JSON.parse(response);
                if (reviews.length > 0) {
                    let content = '';
                    reviews.forEach(review => {
                        content += `
                            <div class="border-bottom mb-3 pb-2">
                                <strong>Rating:</strong> ${'⭐'.repeat(review.rating)}<br>
                                <strong>Comment:</strong> ${review.comment}<br>
                                <em class="text-muted">By User #${review.user_id}</em>
                            </div>
                        `;
                    });
                    $('#reviewContent').html(content);
                } else {
                    $('#reviewContent').html('<p class="text-center">No reviews yet.</p>');
                }
            },
            error: function () {
                $('#reviewContent').html('<p class="text-center text-danger">Failed to load reviews.</p>');
            }
        });
    }

        
        // Confirm Order function
        async function confirmOrder() {
    const productId = document.getElementById('modalProductId').value;
    const quantity = parseInt(document.getElementById('quantity').value);
    const price = parseFloat(document.getElementById('modalProductPrice').value);
    
    // Get user ID from session (make sure this is set in your PHP)
    const userId = <?php echo json_encode($_SESSION['user_id'] ?? null); ?>;

    if (!userId) {
        alert('Please login first');
        return;
    }

    const orderData = {
    user_id: userId,
    total_price: (price * quantity).toFixed(2),
    order_details: [{
        product_id: productId,
        quantity: quantity,
        subtotal: (price * quantity).toFixed(2)
    }]
};


    console.log('Sending order data:', orderData); // Debug

    try {
        const response = await fetch('placeorder.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(orderData)
        });

        const result = await response.json();
        console.log('Server response:', result); // Debug

        if (result.success) {
            alert(`Order #${result.order_id} placed successfully!`);
            orderModal.hide();
            // Optional: Refresh product list
            location.reload();
        } else {
            alert(`Error: ${result.error}`);
        }
    } catch (error) {
        console.error('Order failed:', error);
        alert('Failed to place order. Check console for details.');
    }
}

        function updateSubtotal() {
    const price = parseFloat(document.getElementById('modalProductPrice').value);
    const qty = parseInt(document.getElementById('quantity').value) || 1;
    const subtotal = price * qty;
    document.getElementById('subtotalDisplay').textContent = '₱' + subtotal.toFixed(2);
}
   
        // Search functionality
        document.getElementById('searchButton').addEventListener('click', function() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const products = document.querySelectorAll('.product-card');
            
            products.forEach(product => {
                const productName = product.querySelector('.card-title').textContent.toLowerCase();
                if (productName.includes(searchTerm)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        });
        
        // Sort functionality
        document.getElementById('sortSelect').addEventListener('change', function() {
            const sortValue = this.value;
            const productGrid = document.getElementById('productGrid');
            const products = Array.from(document.querySelectorAll('.product-card'));
            
            products.sort((a, b) => {
                const nameA = a.querySelector('.card-title').textContent;
                const nameB = b.querySelector('.card-title').textContent;
                const priceA = parseFloat(a.querySelector('.card-text').textContent.replace('₱', '').replace(',', ''));
                const priceB = parseFloat(b.querySelector('.card-text').textContent.replace('₱', '').replace(',', ''));
                
                if (sortValue === 'name-asc') {
                    return nameA.localeCompare(nameB);
                } else if (sortValue === 'name-desc') {
                    return nameB.localeCompare(nameA);
                } else if (sortValue === 'price-asc') {
                    return priceA - priceB;
                } else if (sortValue === 'price-desc') {
                    return priceB - priceA;
                }
                return 0;
            });
            
            // Clear and re-append sorted products
            productGrid.innerHTML = '';
            products.forEach(product => {
                productGrid.appendChild(product);
            });
        });
        
        // Filter functionality
        document.getElementById('filterSelect').addEventListener('change', function() {
            const filterValue = this.value.toLowerCase();
            const products = document.querySelectorAll('.product-card');
            
            if (filterValue === 'all') {
                products.forEach(product => {
                    product.style.display = 'block';
                });
                return;
            }
            
            products.forEach(product => {
                // In a real implementation, you would have category data in your product
                // For now, we'll just filter based on product name containing the category
                const productName = product.querySelector('.card-title').textContent.toLowerCase();
                if (productName.includes(filterValue) || filterValue === 'all') {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>

<?php

$conn = null;

?>
