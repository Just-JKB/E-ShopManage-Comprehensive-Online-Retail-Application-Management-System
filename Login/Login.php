<?php
session_start();

// Dummy users: username => [password, role]
$users = [
    "admin" => ["admin", "admin"],
    "customer" => ["customer", "customer"]
];

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if (isset($users[$username]) && $users[$username][0] === $password) {
    $_SESSION['username'] = $username;
    $_SESSION['role'] = $users[$username][1];

    if ($_SESSION['role'] === "admin") {
        header("Location: admin.php");
        exit();
    } else {
        header("Location: dashboard.php");
        exit();
    }
} else {
    echo "<h3>Invalid username or password!</h3>";
}
?>

