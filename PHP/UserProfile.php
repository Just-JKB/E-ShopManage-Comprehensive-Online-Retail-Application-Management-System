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
    <link rel="stylesheet" href="../CSS/UserProfile.css">
    
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse" style="height: 100vh;">
                <div class="position-sticky pt-3">
                    <div class="sidebar-header mb-4">
                        <h3 class="text-light text-center">E-SHOP<br>MANAGE</h3>
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
