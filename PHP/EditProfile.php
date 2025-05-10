<?php
require_once 'dbConnection.php';

// Simulate logged-in user (replace with session logic)
$user_id = 1;

$db = new Database();
$conn = $db->getConnection();

// Fetch user data
$stmt = $conn->prepare("CALL GetUserById(?)");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// If the form is submitted, update the user's data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];

    // Update user data in the database
    $stmt = $conn->prepare("CALL UpdateUserInfo(?, ?, ?, ?, ?)");
    $updateStmt->execute([$name, $email, $contact_number, $address, $user_id]);

    // Redirect back to profile page after update
    header("Location: UserProfile.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <style>
       body  {
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
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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

        .profile-info input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .actions {
            margin-top: 30px;
            display: flex;
            justify-content: space-between;
        }

        .actions button,
        .actions a {
            text-decoration: none;
            padding: 10px 20px;
            background-color: #3498db;
            color: #fff;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s;
        }

        .actions button:hover,
        .actions a:hover {
            background-color: #2980b9;
        }
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

            <label for="contact_number">Contact Number</label>
            <input type="text" id="contact_number" name="contact_number" value="<?= htmlspecialchars($user['contact_number']) ?>" required>

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