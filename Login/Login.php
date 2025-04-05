<?php
// login.php

session_start();

// Hardcoded username and password
$valid_username = "admin";
$valid_password = "name"; // In real apps, use hashed passwords

// Get form data
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Check credentials
if ($username === $valid_username && $password === $valid_password) {
    $_SESSION['username'] = $username;
    echo "Login successful! Welcome, $username.";
    // Redirect to protected page if needed
    // header("Location: dashboard.php");
} else {
    echo "Invalid username or password.";
}
?>
