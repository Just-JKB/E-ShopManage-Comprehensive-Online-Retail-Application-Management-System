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

// Fetch orders for the logged-in user
$userId = $_SESSION['user_id'];
$orderStmt = $conn->prepare("
    SELECT o.order_id, o.order_date, o.total_price, o.status,
       p.product_name, od.quantity, od.subtotal, p.image_url, od.product_id
    FROM orders o
    JOIN order_details od ON o.order_id = od.order_id
    JOIN products p ON od.product_id = p.product_id
    WHERE o.user_id = :user_id
    ORDER BY o.order_date DESC
");



$orderStmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
$orderStmt->execute();
$orders = $orderStmt->fetchAll(PDO::FETCH_ASSOC);

// Close connection
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/OrderDashboard.css">
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
                        <li class="nav-item"><a class="nav-link" href="UserDashboard.php"><i class="fas fa-home me-2"></i>Home</a></li>
                        <li class="nav-item"><a class="nav-link active" href="#"><i class="fas fa-shopping-bag me-2"></i>My Orders</a></li>
                        <li class="nav-item"><a class="nav-link" href="UserProfile.php"><i class="fas fa-user me-2"></i>My Profile</a></li>
                        <li class="nav-item mt-5"><a class="nav-link text-danger" href="../PHP/userLogout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">My Orders</h1>
                    <a href="UserProfile.php"><img src="images/profile.png" alt="Profile" class="rounded-circle border" width="40"></a>
                </div>

                <?php if (empty($orders)): ?>
                    <div class="alert alert-warning" role="alert">
                        You have no orders placed yet. Start shopping now!
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order Date</th>
                                    <th>Total Price</th>
                                    <th>Status</th>
                                    <th>Details</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= date('F j, Y', strtotime($order['order_date'])) ?></td>
                                    <td>₱<?= number_format($order['total_price'], 2) ?></td>
                                    <td><?= htmlspecialchars($order['status']) ?></td>
                                    <td>
                                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#orderDetailsModal" 
                                        data-order-items='<?= json_encode($order, JSON_HEX_APOS | JSON_HEX_QUOT) ?>'>
                                            View Details
                                        </button>
                                        <?php if ($order['status'] === 'Delivered'): ?>
                                           <button 
                                                class="btn btn-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#reviewModal" 
                                                data-order-id="<?= $order['order_id'] ?>" 
                                                data-product-id="<?= $order['product_id'] ?>" 
                                            >
                                                Review Product
                                            </button>

                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>

                        </table>
                    </div>
                <?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="orderDetailsModalLabel">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="orderDetailsContent">
                    <p>Loading order details...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">Leave a Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="reviewForm">
                    <input type="hidden" id="orderId" name="order_id">
                    <input type="hidden" id="productId" name="product_id">
                    <input type="hidden" name="user_id" value="<?= $_SESSION['user_id'] ?>">

                    <div class="mb-3">
                        <label for="rating" class="form-label">Rating (1 to 5)</label>
                        <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" required>
                    </div>
                    <div class="mb-3">
                        <label for="comment" class="form-label">Comment</label>
                        <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Submit Review</button>
                </form>
            </div>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const orderDetailsModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));

        document.getElementById('orderDetailsModal').addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Button that triggered the modal
            const orderData = JSON.parse(button.getAttribute('data-order-items')); // Extract order details

            const orderDetailsContent = document.getElementById('orderDetailsContent');
            const orderId = orderData.order_id;
            const productName = orderData.product_name;
            const quantity = orderData.quantity;
            const subtotal = orderData.subtotal;

            orderDetailsContent.innerHTML = `
            <img src="${orderData.image_url}" alt="${productName}" class="img-fluid rounded mb-3" style="max-height: 200px;">
            <p><strong>Product Name:</strong> ${productName}</p>
            <p><strong>Quantity:</strong> ${quantity}</p>
            <p><strong>Subtotal:</strong> ₱${subtotal}</p>
            <p><strong>Status:</strong> ${orderData.status}</p>
        `;

        });

        document.getElementById("reviewModal").addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const orderId = button.getAttribute('data-order-id');
    const productId = button.getAttribute('data-product-id'); // You'll need to pass this from PHP

    document.getElementById('orderId').value = orderId;
    document.getElementById('productId').value = productId;
});

document.getElementById('reviewForm').addEventListener('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);

    fetch('submitReview.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.text())
    .then(response => {
        alert(response);
        document.getElementById('reviewForm').reset();
        bootstrap.Modal.getInstance(document.getElementById('reviewModal')).hide();
    })
    .catch(err => alert("Error submitting review: " + err));
});
    </script>
</body>
</html>
