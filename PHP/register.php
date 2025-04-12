<?php
require_once '../PHP/dbConnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];

    if ($password !== $confirm_password) {
        echo "Passwords do not match!";
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $database = new Database();
    $conn = $database->getConnection();

    try {
        if ($role === 'user') {
            $stmt = $conn->prepare("CALL RegisterUser(:name, :email, :password, :contact, :address)");
        } elseif ($role === 'admin') {
            $stmt = $conn->prepare("CALL RegisterAdmin(:name, :email, :password, :contact, :address)");
        } else {
            echo "Invalid role selected.";
            exit();
        }

        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':contact', $contact_number);
        $stmt->bindParam(':address', $address);

        $stmt->execute();
        echo "Account created successfully as <strong>$role</strong>! <a href='../HTML/login.html'>Login here</a>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
