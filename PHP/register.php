<?php
require_once '../PHP/dbConnection.php';

function validatePasswords($password, $confirm_password) {
    if ($password !== $confirm_password) {
        return "Passwords do not match!";
    }
    return null;
}

function registerAccount($conn, $name, $email, $hashed_password, $contact_number, $address) {
    $stmt = $conn->prepare("CALL RegisterUser(:name, :email, :password, :contact, :address)");

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':contact', $contact_number);
    $stmt->bindParam(':address', $address);
    $stmt->execute();

    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];

    $error = validatePasswords($password, $confirm_password);
    if ($error) {
        echo $error;
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $database = new Database();
    $conn = $database->getConnection();

    try {
        $result = registerAccount($conn, $name, $email, $hashed_password, $contact_number, $address);

        if ($result === true) {
            echo "Account created successfully! <a href='../HTML/login.html'>Login here</a>";
        } else {
            echo $result;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
