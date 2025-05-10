<?php
require_once '../PHP/dbConnection.php'; // Adjust if needed
require_once '../PHP/insertFunction.php';

// [1] Database connection
$db = new Database();
$conn = $db->getConnection();

// [2] CATEGORY QUERY
$categoryStmt = $conn->query("SELECT category_id, category_name FROM categories");
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

// [3] PRODUCT QUERY
$productStmt = $conn->query("SELECT * FROM products");
$products = $productStmt->fetchAll(PDO::FETCH_ASSOC);

echo '<!-- Debug: ';
if (empty($products)) {
    echo 'No products found';
} else {
    echo 'Found ' . count($products) . ' products. First product: ';
    print_r($products[0]);
}
echo ' -->';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management - E-Shop Admin</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/ProductManagement.css">
</head>
<body>
    <div id="successBanner" class="alert alert-success text-center m-0" style="display: none;">
         Product added successfully!
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="sidebar-header mb-4">
                        <h3 class="text-light text-center">PRODUCT MANAGEMENT</h3>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="AdminDashboard.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../PHP/InventoryManagement.php">
                                <i class="fas fa-boxes me-2"></i>
                                Inventory Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="#">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Product Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../PHP/UserManagement.php">
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
                            <a class="nav-link text-danger" href="../HTML/adminLogin.html">
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
                    <h1 class="h2">Product Management</h1>
                    <div>
                        <img src="https://via.placeholder.com/40" alt="Profile" class="rounded-circle border">
                    </div>
                </div>

                <div class="container-fluid p-0">
                    <div class="d-flex justify-content-center mb-4">
                        <input type="text" class="form-control me-2" id="productSearch" placeholder="Search products..." />
                        <button class="btn btn-primary" id="searchBtn">
                            <i class="fas fa-search"></i> Search
                        </button>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <label class="me-2">Sort by:</label>
                            <select id="sortSelect" class="form-select d-inline-block w-auto">
                                <option value="name-asc">Name (A-Z)</option>
                                <option value="name-desc">Name (Z-A)</option>
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
                                <option value="hoodies">HOODIES</option>
                                <option value="jackets">JACKETS</option>
                                <option value="long-sleeves">LONG SLEEVES</option>
                                <option value="polos">POLOS</option>
                                <option value="sando">SANDO</option>
                                <option value="shorts">SHORTS</option>
                                <option value="skirts">SKIRTS</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Product Grid - Fixed Structure -->
                    <div class="row g-4" id="productGrid">
                        <!-- Add Product Card -->
                        <div class="col-6 col-md-4 col-lg-3" id="addProductCard">
                            <div class="d-flex flex-column justify-content-center align-items-center border rounded p-4 h-100 bg-white shadow-sm" style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#addProductModal">
                                <div class="fs-1">+</div>
                                <div>Add Product</div>
                            </div>
                        </div>

                        <!-- Product Cards -->
                        <?php foreach ($products as $product): ?>
                        <div class="col-6 col-md-4 col-lg-3 product-card" data-product-id="<?= $product['product_id'] ?>">
                            <div class="card shadow-sm h-100">
                                <img src="<?= '../' . htmlspecialchars($product['image_url'] ?? 'images/default-product.jpg') ?>" 
                                    class="card-img-top" 
                                    alt="<?= htmlspecialchars($product['product_name'] ?? 'Product image') ?>">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($product['product_name'] ?? 'Unknown Product') ?></h5>
                                    <p class="card-text">₱<?= number_format($product['price'] ?? 0, 2) ?></p>
                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editProductModal">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-danger btn-sm delete-btn" data-product-id="<?= $product['product_id'] ?>">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addProductForm" action="../PHP/insertFunction.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Product Name</label>
                            <input type="text" class="form-control" id="productName" name="productName" required>
                        </div>

                        <div class="mb-3">
                            <label for="productDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="productDescription" name="productDescription" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="productPrice" class="form-label">Price</label>
                            <input type="number" class="form-control" id="productPrice" name="productPrice" step="0.01" required>
                        </div>

                        <div class="mb-3">
                            <label for="productCategory" class="form-label">Category</label>
                            <select class="form-select" id="productCategory" name="productCategory" required>
                                <option value="">Select a category</option>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?= htmlspecialchars($category['category_id']) ?>">
                                        <?= htmlspecialchars($category['category_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="productSize" class="form-label">Size</label>
                            <select class="form-select" id="productSize" name="productSize" required>
                                <option value="">Select size</option>
                                <option value="XS">XS</option>
                                <option value="S">S</option>
                                <option value="M">M</option>
                                <option value="L">L</option>
                                <option value="XL">XL</option>
                                <option value="XXL">XXL</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="productColor" class="form-label">Color</label>
                            <input type="text" class="form-control" id="productColor" name="productColor" required>
                        </div>

                        <div class="mb-3">
                            <label for="productStock" class="form-label">Stock Quantity</label>
                            <input type="number" class="form-control" id="productStock" name="productStock" required>
                        </div>

                        <div class="mb-3">
                            <label for="productImage" class="form-label">Product Image</label>
                            <input type="file" class="form-control" id="productImage" name="productImage">
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="editProductForm" method="POST" enctype="multipart/form-data" action="../PHP/updateProduct.php">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="editProductId" name="productId">

                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" class="form-control" id="editProductName" name="productName" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="editProductDescription" name="productDescription"></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Price</label>
                        <input type="number" class="form-control" id="editProductPrice" name="productPrice" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" id="editProductCategory" name="productCategory" required>
                            <option value="">Select a category</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= $category['category_id'] ?>">
                                    <?= $category['category_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Size</label>
                        <select class="form-select" id="editProductSize" name="productSize" required>
                            <option value="XS">XS</option>
                            <option value="S">S</option>
                            <option value="M">M</option>
                            <option value="L">L</option>
                            <option value="XL">XL</option>
                            <option value="XXL">XXL</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Color</label>
                        <input type="text" class="form-control" id="editProductColor" name="productColor" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stock Quantity</label>
                        <input type="number" class="form-control" id="editProductStock" name="productStock" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="editProductImage" name="productImage">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save Changes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('click', function (e) {
    if (e.target.closest('.btn-warning')) {
        const card = e.target.closest('.product-card');
        const productId = card.getAttribute('data-product-id');
        const productName = card.querySelector('.card-title').textContent;
        const price = card.querySelector('.card-text').textContent.replace('₱', '');

        // Optional: use dataset or fetch more details via AJAX if needed
        const product = <?php echo json_encode($products); ?>.find(p => p.product_id == productId);

        if (product) {
            document.getElementById('editProductId').value = product.product_id;
            document.getElementById('editProductName').value = product.product_name;
            document.getElementById('editProductDescription').value = product.description;
            document.getElementById('editProductPrice').value = product.price;
            document.getElementById('editProductCategory').value = product.category_id;
            document.getElementById('editProductSize').value = product.size;
            document.getElementById('editProductColor').value = product.color;
            document.getElementById('editProductStock').value = product.stock_quantity;
        }

        const modal = new bootstrap.Modal(document.getElementById('editProductModal'));
        modal.show();
    }
});
</script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const deleteButtons = document.querySelectorAll('.delete-btn');

            deleteButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const productId = this.getAttribute('data-product-id');
                    
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Send delete request to PHP
                            fetch('../PHP/deleteProduct.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({ productId: productId })
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire(
                                        'Deleted!',
                                        'Your product has been deleted.',
                                        'success'
                                    ).then(() => {
                                        location.reload(); // Reload the page to reflect changes
                                    });
                                } else {
                                    Swal.fire(
                                        'Error!',
                                        'There was an issue deleting the product.',
                                        'error'
                                    );
                                }
                            })
                            .catch(error => {
                                Swal.fire(
                                    'Error!',
                                    'An error occurred while deleting the product.',
                                    'error'
                                );
                            });
                        }
                    });
                });
            });
        });

        document.getElementById('searchBtn').addEventListener('click', function () {
    let searchQuery = document.getElementById('productSearch').value;
    let categoryFilter = document.getElementById('filterSelect').value;

    fetch('../PHP/searchProducts.php', {
        method: 'POST',
        body: new URLSearchParams({
            searchQuery: searchQuery,
            categoryFilter: categoryFilter
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            let productGrid = document.getElementById('productGrid');
            productGrid.innerHTML = ''; // Clear existing products

            data.products.forEach(product => {
                let productCard = `
                    <div class="col-6 col-md-4 col-lg-3 product-card" data-product-id="${product.product_id}">
                        <div class="card shadow-sm h-100">
                            <img src="../${product.image_url || 'images/default-product.jpg'}" class="card-img-top" alt="${product.product_name}">
                            <div class="card-body">
                                <h5 class="card-title">${product.product_name}</h5>
                                <p class="card-text">₱${product.price}</p>
                                <div class="d-flex justify-content-between">
                                    <button class="btn btn-warning btn-sm">Edit</button>
                                    <button class="btn btn-danger btn-sm delete-btn" data-product-id="${product.product_id}">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                productGrid.innerHTML += productCard;
            });
        } else {
            console.error('Search failed:', data.message);
        }
    })
    .catch(error => console.error('Error:', error));
});

document.getElementById("addProductForm").addEventListener("submit", function (e) {
    e.preventDefault(); // Prevent default form submission

    const form = e.target;
    const formData = new FormData(form);

    fetch("../PHP/insertFunction.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                title: "Success!",
                text: data.message || "Product added successfully!",
                icon: "success",
                confirmButtonText: "OK"
            }).then(() => {
                // Optional: Reload to see the new product
                location.reload();
            });

            // Optionally reset the form and close modal
            form.reset();
            const modal = bootstrap.Modal.getInstance(document.getElementById("addProductModal"));
            modal.hide();
        } else {
            Swal.fire({
                title: "Error!",
                text: data.message || "An error occurred.",
                icon: "error"
            });
        }
    })
    .catch(err => {
        Swal.fire({
            title: "Error!",
            text: "Request failed. Please try again.",
            icon: "error"
        });
        console.error("Fetch error:", err);
    });
});

    </script>
</body>
</html>



