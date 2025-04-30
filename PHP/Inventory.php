<?php
require_once '../PHP/dbConnection.php'; // Adjust if needed
require_once '../PHP/insertFunction.php';

// [1] Database connection
$db = new Database();
$conn = $db->getConnection();

// [2] CATEGORY QUERY (PUT #1 HERE)
$categoryStmt = $conn->query("SELECT category_id, category_name FROM categories");
$categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);

// [3] PRODUCT QUERY (existing code)
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
    <style>
        body {
            font-size: .875rem;
            background-color: #f8f9fa;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 100;
            padding: 48px 0 0;
            box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
            height: 100vh;
        }

        .sidebar-header {
            padding: 0.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link {
            font-weight: 500;
            color: #ced4da;
            padding: 0.75rem 1rem;
            margin-bottom: 0.25rem;
        }

        .sidebar .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.2);
        }

        main {
            margin-left: 16.66667%;
        }

        @media (max-width: 767.98px) {
            main {
                margin-left: 0;
            }
            .sidebar {
                position: static;
                height: auto;
                padding-top: 0;
            }
        }
        
        .product-card img {
            width: 100%;
            height: auto;
        }

        .product-name {
            text-align: center;
            margin-top: 0.5rem;
        }

        #productSearch {
            max-width: 300px;
        }

        #searchBtn {
            padding: 0.375rem 0.75rem;
        }

        .product-item {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .product-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
        }

        .product-item img {
            object-fit: cover;
            height: 180px;
            width: 100%;
        }

        .product-item h6 {
            font-weight: 600;
            font-size: 1rem;
        }

        .product-item p {
            margin-bottom: 0.5rem;
        }

        .product-item .btn {
            min-width: 48%;
        }
    </style>
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
                            <a class="nav-link" href="index.php">
                                <i class="fas fa-tachometer-alt me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../PHP/inventoryyy.php">
                                <i class="fas fa-boxes me-2"></i>
                                Inventory Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="../PHP/inventory.php">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Product Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../PHP/users.php">
                                <i class="fas fa-users me-2"></i>
                                User Management
                            </a>
                        </li>
                        <li class="nav-item mt-5">
                            <a class="nav-link text-danger" href="logout.php">
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
                <!-- Modal Header (keep this) -->
                <div class="modal-header">
                    <h5 class="modal-title" id="addProductModalLabel">Add Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <!-- Modal Form -->
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

                    <!-- Modal Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
                            
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
// Wait for page to fully load
document.addEventListener('DOMContentLoaded', function () {
    // Product Form Submission
    document.getElementById('addProductForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';
        submitBtn.disabled = true;

        try {
            const formData = new FormData(this);
            
            const response = await fetch(this.action, {
                method: this.method,
                body: formData
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const result = await response.json();
            console.log('Server response:', result);

            if (result.success) {
                // Show success SweetAlert
                await Swal.fire({
                    icon: 'success',
                    title: 'Product Added!',
                    text: result.message || 'The product was successfully added.',
                    confirmButtonColor: '#28a745', // green button
                    confirmButtonText: 'OK'
                }).then(() => {
                    // Refresh the page after clicking OK
                    window.location.reload();
                });

                // Hide modal and reset form
                const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
                if (modal) modal.hide();
                this.reset();
            } else {
                // Show error SweetAlert
                await Swal.fire({
                    icon: 'error',
                    title: 'Failed to Add Product',
                    text: result.message || 'Please try again.',
                    confirmButtonColor: '#dc3545', // red button
                    confirmButtonText: 'OK'
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: error.message || 'An unexpected error occurred. Please try again.',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'OK'
            });
        } finally {
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        }
    });

    // Add event listeners to all delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            deleteProduct(productId);
        });
    });

    // Delete product function
    function deleteProduct(productId) {
        console.log('Delete product called with ID:', productId);
        
        if (!productId) {
            console.error('No product ID provided');
            return;
        }
        
        Swal.fire({
            title: 'Are you sure?',
            text: 'You will not be able to recover this product!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading state
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Create form data
                const formData = new FormData();
                formData.append('product_id', productId);
                
                // Send delete request
                fetch('../PHP/deleteProduct.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Delete response:', data);
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: data.message || 'Product has been deleted.',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message || 'Failed to delete product.',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while deleting the product.',
                        confirmButtonColor: '#dc3545'
                    });
                });
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('productSearch');
    const searchButton = document.getElementById('searchBtn');
    const productCards = document.querySelectorAll('.product-card');

    // Filter function
    function filterProducts() {
        const searchValue = searchInput.value.toLowerCase().trim();

        productCards.forEach(card => {
            const productName = card.querySelector('.card-title').textContent.toLowerCase();

            if (productName.includes(searchValue)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Trigger on button click
    searchButton.addEventListener('click', filterProducts);

    // Optional: Trigger search on "Enter" key press
    searchInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            filterProducts();
        }
    });

    // Keep your existing form submission and delete logic here...
});

</script>

</body>
</html>
