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

// If the form is submitted, update the user's data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    // Update user data in the database
    $updateStmt = $conn->prepare("UPDATE users SET name = ?, email = ?, address = ? WHERE user_id = ?");
    $updateStmt->execute([$name, $email, $address, $user_id]);

    // Redirect back to profile page after update
    header("Location:UserProfile.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <style>
        /* Add your styles here */
    </style>
</head>
<body>

<div class="container">
    <h1>Edit Profile</h1>

    <form method="POST" action="EditProfile.php">
        <div class="profile-info">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="address">Address</label>
            <input type="text" id="address" name="address" value="<?= htmlspecialchars($user['address']) ?>" required>
        </div>

        <div class="actions">
            <button type="submit">Save Changes</button>
            <a href="UserProfile.php" style="padding: 10px 20px; background-color: #e74c3c; color: white; text-decoration: none; border-radius: 5px;">Cancel</a>
        </div>
    </form>
</div>

</body>
</html>
