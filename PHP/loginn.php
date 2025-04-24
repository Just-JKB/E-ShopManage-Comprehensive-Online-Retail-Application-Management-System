<?php
session_start();
require_once '../dbConnection.php'; 

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $role = $_POST['role'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($role) || empty($email) || empty($password)) {
        die("Please fill in all fields.");
    }

    $db = new Database();
    $conn = $db->getConnection();

    if ($role === "admin") {
        $query = "SELECT * FROM admin WHERE email = :email";
    } else {
        $query = "SELECT * FROM user WHERE email = :email";
    }

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $role;

        if ($role === "admin") {
            header("Location: inventory.php"); 
        } else {
            header("Location: user_dashboard.php"); 
        }
        exit();
    } else {
        echo "<script>alert('Invalid credentials.'); window.location.href='../HTML/login.html';</script>";
        exit();
    }
}
?>