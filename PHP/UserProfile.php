<?php
require_once 'dbConnection.php';

// Simulate logged-in user (replace with session logic)
$user_id = 1;

$db = new Database();
$conn = $db->getConnection();

// Fetch user data
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Profile Dashboard</title>
    <style>
       body {
            font-family: Arial, sans-serif;
            background: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #2c3e50;
        }

        .profile-info {
            margin-top: 20px;
        }

        .profile-info label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
            color: #555;
        }

        .profile-info div {
            margin-bottom: 10px;
        }

        .actions {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .actions a {
            text-decoration: none;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .actions a:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>My Profile</h1>

    <div class="profile-info">
        <label>Full Name</label>
        <div><?= htmlspecialchars($user['name']) ?></div>

        <label>Email</label>
        <div><?= htmlspecialchars($user['email']) ?></div>

        <label>Address</label>
        <div><?= htmlspecialchars($user['address']) ?></div>
    </div>

    <div class="actions">
        <!-- Link to Edit Profile Page -->
        <a href="EditProfile.php" style="padding: 10px 20px; background-color: #3498db; color: white; text-decoration: none; border-radius: 5px;">Edit Profile</a>
        <a href="UserDashboard.php" style="padding: 10px 20px; background-color: #2ecc71; color: white; text-decoration: none; border-radius: 5px;">Back to Shop</a>
    </div>
</div>

</body>
</html>