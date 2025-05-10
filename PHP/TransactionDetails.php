<?php
session_start();
require_once 'dbConnection.php';

$database = new Database();
$conn = $database->getConnection();

// Fetch all order details using the stored procedure
try {
    $stmt = $conn->prepare("CALL GetAllOrdersWithDetails()");
    $stmt->execute();
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Transaction Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/TransactionDetails.css">
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
            <div class="position-sticky pt-3">
                <div class="sidebar-header mb-4">
                    <h3 class="text-light text-center">ADMIN DASHBOARD</h3>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="AdminDashboard.php">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="InventoryManagement.php">
                            <i class="fas fa-boxes me-2"></i>
                            Inventory Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ProductManagement.php">
                            <i class="fas fa-shopping-cart me-2"></i>
                            Product Management
                        </a> 
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="UserManagement.php">
                            <i class="fas fa-users me-2"></i>
                            User Management
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active text-info" href="TransactionDetails.php">
                            <i class="fas fa-receipt me-2"></i>
                            Transaction Details
                        </a>
                    </li>
                    <li class="nav-item mt-5">
                        <a class="nav-link text-danger" href="adminLogout.php">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10">
            <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                <h2>Transaction Details</h2>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>User ID</th>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Total Price</th>
                            <th>Order Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= $order['order_id'] ?></td>
                            <td><?= $order['user_id'] ?></td>
                            <td><?= $order['product_name'] ?></td>
                            <td><?= $order['quantity'] ?></td>
                            <td><?= number_format($order['total_price'], 2) ?></td>
                            <td><?= $order['order_date'] ?></td>
                            <td>
                                <select class="form-select status-select" data-order-id="<?= $order['order_id'] ?>">
                                    <option <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                    <option <?= $order['status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                    <option <?= $order['status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                    <option <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<!-- Scripts -->
<script>
    document.querySelectorAll('.status-select').forEach(select => {
        select.addEventListener('change', function () {
            const orderId = this.dataset.orderId;
            const newStatus = this.value;

            fetch('updateOrderStatus.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `order_id=${orderId}&status=${newStatus}`
            })
            .then(response => response.text())
            .then(data => alert(data))
            .catch(err => alert("Error: " + err));
        });
    });
</script>

</body>
</html>
