<?php
require_once '../PHP/dbConnection.php'; // Make sure this path is correct

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

    // Connect using the Database class
    $database = new Database();
    $conn = $database->getConnection();

    try {
        // Call the stored procedure using PDO
        $stmt = $conn->prepare("CALL RegisterUser(:name, :email, :password, :contact, :address)");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':contact', $contact_number);
        $stmt->bindParam(':address', $address);

        $stmt->execute();
        echo "Account created successfully! <a href='../HTML/login.html'>Login here</a>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
