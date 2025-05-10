<?php
session_start();
require_once 'dbConnection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: userLogin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(to right, #74ebd5, #9face6);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
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

        .profile-container {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }

        .profile-container h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        .profile-section {
            margin-bottom: 20px;
        }

        .profile-label {
            font-weight: 600;
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .profile-value {
            font-size: 16px;
            color: #2c3e50;
            background: #f9f9f9;
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
        }

        .actions {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .actions a {
            text-decoration: none;
            padding: 12px 25px;
            font-weight: 600;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .edit-btn {
            background: #3498db;
            color: #fff;
        }

        .edit-btn:hover {
            background: #2980b9;
        }

        .back-btn {
            background: #2ecc71;
            color: #fff;
        }

        .back-btn:hover {
            background: #27ae60;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse" style="height: 100vh;">
                <div class="position-sticky pt-3">
                    <div class="sidebar-header mb-4">
                        <h3 class="text-light text-center">USER<br>DASHBOARD</h3>
                    </div>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="UserDashboard.php">
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
                            <a class="nav-link active" href="UserProfile.php">
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

            <!-- Profile Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 d-flex justify-content-center align-items-center" style="height: 100vh;">
                <div class="profile-container">
                    <h1>My Profile</h1>

                    <div class="profile-section">
                        <div class="profile-label">Full Name</div>
                        <div class="profile-value"><?= htmlspecialchars($user['name']) ?></div>
                    </div>

                    <div class="profile-section">
                        <div class="profile-label">Email</div>
                        <div class="profile-value"><?= htmlspecialchars($user['email']) ?></div>
                    </div>

                    <div class="profile-section">
                        <div class="profile-label">Address</div>
                        <div class="profile-value"><?= htmlspecialchars($user['address']) ?></div>
                    </div>

                    <div class="actions">
                        <a href="EditProfile.php" class="edit-btn">Edit Profile</a>
                        <a href="UserDashboard.php" class="back-btn">Back to Shop</a>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap & FontAwesome (Add to <head> if not already included) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</body>
</html>
