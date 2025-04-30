<?php
// Database connection
require_once '../PHP/dbConnection.php';

// Create database instance and get connection
$database = new Database();
$pdo = $database->getConnection();

// Get users data using PDO
$query = "SELECT * FROM users ORDER BY user_id DESC";
$stmt = $pdo->query($query);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if status column exists in users table
$tableInfo = $pdo->query("DESCRIBE users");
$columns = $tableInfo->fetchAll(PDO::FETCH_COLUMN);
$hasStatusColumn = in_array('status', $columns);

// If status column doesn't exist, add it
if (!$hasStatusColumn) {
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN status VARCHAR(20) DEFAULT 'active'");
        // Update the users array to include the new status column
        foreach ($users as &$user) {
            $user['status'] = 'active';
        }
    } catch (PDOException $e) {
        // If there's an error, just continue - we'll handle missing status gracefully
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - E-Shop Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        
        .badge-banned {
            background-color: #dc3545;
            color: white;
        }
        
        .badge-active {
            background-color: #28a745;
            color: white;
        }
        
        .action-buttons {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
                <div class="position-sticky pt-3">
                    <div class="sidebar-header mb-4">
                        <h3 class="text-light text-center">USER MANAGEMENT</h3>
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
                            <a class="nav-link" href="../PHP/inventory.php">
                                <i class="fas fa-shopping-cart me-2"></i>
                                Product Management
                            </a> 
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="../PHP/users.php">
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
                    <h1 class="h2">User Management</h1>
                </div>
                
                <!-- Users Table -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary">Registered Users</h6>
                        <div>
                            <button class="btn btn-sm btn-outline-success" id="refreshTable">
                                <i class="fas fa-sync-alt"></i> Refresh
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="usersTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Contact Number</th>
                                        <th>Address</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr data-user-id="<?= htmlspecialchars($user['user_id']) ?>">
                                            <td><?= htmlspecialchars($user['user_id']) ?></td>
                                            <td><?= htmlspecialchars($user['name']) ?></td>
                                            <td><?= htmlspecialchars($user['email']) ?></td>
                                            <td><?= htmlspecialchars($user['contact_number'] ?? 'N/A') ?></td>
                                            <td><?= htmlspecialchars($user['address'] ?? 'N/A') ?></td>
                                            <td>
                                                <?php 
                                                $status = $user['status'] ?? 'active';
                                                $badgeClass = $status === 'banned' ? 'badge-banned' : 'badge-active';
                                                ?>
                                                <span class="badge rounded-pill <?= $badgeClass ?>"><?= ucfirst($status) ?></span>
                                            </td>
                                            <td class="action-buttons">
                                                <?php if ($status !== 'banned'): ?>
                                                <button class="btn btn-sm btn-warning ban-user" data-user-id="<?= $user['user_id'] ?>">
                                                    <i class="fas fa-ban"></i> Ban
                                                </button>
                                                <?php else: ?>
                                                <button class="btn btn-sm btn-success unban-user" data-user-id="<?= $user['user_id'] ?>">
                                                    <i class="fas fa-check"></i> Unban
                                                </button>
                                                <?php endif; ?>
                                                <button class="btn btn-sm btn-danger delete-user" data-user-id="<?= $user['user_id'] ?>">
                                                    <i class="fas fa-trash"></i> Delete
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#usersTable').DataTable({
                "order": [[0, "desc"]] // Sort by ID descending by default
            });
            
            // Handle delete user
            $(document).on('click', '.delete-user', function() {
                const userId = $(this).data('user-id');
                const userName = $(this).closest('tr').find('td:eq(1)').text();
                
                Swal.fire({
                    title: 'Delete User?',
                    html: `Are you sure you want to delete <strong>${userName}</strong>?<br>This action cannot be undone.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete user',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performUserAction(userId, 'delete');
                    }
                });
            });
            
            // Handle ban user
            $(document).on('click', '.ban-user', function() {
                const userId = $(this).data('user-id');
                const userName = $(this).closest('tr').find('td:eq(1)').text();
                
                Swal.fire({
                    title: 'Ban User?',
                    html: `Are you sure you want to ban <strong>${userName}</strong>?<br>They will not be able to log in.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#f0ad4e',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, ban user',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performUserAction(userId, 'ban');
                    }
                });
            });
            
            // Handle unban user
            $(document).on('click', '.unban-user', function() {
                const userId = $(this).data('user-id');
                const userName = $(this).closest('tr').find('td:eq(1)').text();
                
                Swal.fire({
                    title: 'Unban User?',
                    html: `Are you sure you want to unban <strong>${userName}</strong>?<br>They will be able to log in again.`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, unban user',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        performUserAction(userId, 'unban');
                    }
                });
            });
            
            // Refresh table button
            $('#refreshTable').on('click', function() {
                location.reload();
            });
            
            // Function to perform user actions (delete, ban, unban)
            function performUserAction(userId, action) {
                // Show loading state
                Swal.fire({
                    title: 'Processing...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // Create form data
                const formData = new FormData();
                formData.append('user_id', userId);
                formData.append('action', action);
                
                // Send request to server
                fetch('../PHP/userActions.php', {
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
                    console.log('Action response:', data);
                    
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: data.message,
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            // Refresh the page
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: data.message,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An error occurred while processing your request.',
                        confirmButtonColor: '#dc3545'
                    });
                });
            }
        });
    </script>
</body>
</html>
