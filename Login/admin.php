<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="box">
        <h1>Welcome Admin <?php echo $_SESSION['username']; ?>!</h1>
        <a class="logout-link" href="logout.php">Logout</a>
    </div>
</body>
</html>
