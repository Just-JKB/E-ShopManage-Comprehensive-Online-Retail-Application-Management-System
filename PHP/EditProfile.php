<?php
session_start();
require_once 'dbConnection.php';

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../HTML/userLogin.html");
    exit();
}

$user_id = $_SESSION['user_id'];

$db = new Database();
$conn = $db->getConnection();

$stmt = $conn->prepare("CALL GetUserById(?)");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt->closeCursor();


// If the form is submitted, update the user's data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];

    // Call the stored procedure to update user data
    $updateStmt = $conn->prepare("CALL UpdateUserInfo(?, ?, ?, ?, ?)");
    $updateStmt->execute([$user_id, $name, $email, $contact_number, $address]);

    // Redirect back to profile page after update
    header("Location: UserProfile.php");
    exit();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/EditProfile.css">
</head>
<body>

<div class="container">
    <div class="header">
        <h1>Edit Your Profile</h1>
        <p>Update your personal information</p>
    </div>

    <form method="POST" action="EditProfile.php">
        <div class="profile-info">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>" required>
                <i class="fas fa-user input-icon"></i>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                <i class="fas fa-envelope input-icon"></i>
            </div>

            <div class="form-group">
                <label for="contact_number">Contact Number</label>
                <input type="text" id="contact_number" name="contact_number" value="<?= htmlspecialchars($user['contact_number']) ?>" required>
                <i class="fas fa-phone input-icon"></i>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <input type="text" id="address" name="address" value="<?= htmlspecialchars($user['address']) ?>" required>
                <i class="fas fa-home input-icon"></i>
            </div>
        </div>

        <div class="actions">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            <a href="UserProfile.php" class="btn btn-danger"><i class="fas fa-times"></i> Cancel</a>
        </div>
    </form>
</div>

</body>
</html>
