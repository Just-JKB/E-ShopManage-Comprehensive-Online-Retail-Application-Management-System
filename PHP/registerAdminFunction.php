<?php
require_once 'dbConnection.php'; // Make sure path is correct

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];

    // Check if the email already exists
    $checkStmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $checkStmt->execute([$email]);

    if ($checkStmt->rowCount() > 0) {
        // Email already exists - show error alert
        echo '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Registration Error</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "error",
                        title: "Registration Failed",
                        text: "Email already exists!",
                        confirmButtonColor: "#3085d6"
                    }).then((result) => {
                        window.location.href = "../PHP/registerAdmin.php";
                    });
                });
            </script>
        </body>
        </html>';
        exit();
    }

    // Insert admin
    $stmt = $conn->prepare("INSERT INTO admin (name, email, password, contact_number, address) VALUES (?, ?, ?, ?, ?)");
    $success = $stmt->execute([$name, $email, $password, $contact_number, $address]);

    if ($success) {
        // Registration successful - show success alert
        echo '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Registration Successful</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "success",
                        title: "Registration Successful",
                        text: "Admin account has been created!",
                        confirmButtonColor: "#3085d6"
                    }).then((result) => {
                        window.location.href = "../HTML/adminLogin.html";
                    });
                });
            </script>
        </body>
        </html>';
    } else {
        // Registration failed - show error alert
        echo '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Registration Error</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "error",
                        title: "Registration Failed",
                        text: "There was an error creating the admin account. Please try again.",
                        confirmButtonColor: "#3085d6"
                    }).then((result) => {
                        window.location.href = "../PHP/registerAdmin.php";
                    });
                });
            </script>
        </body>
        </html>';
    }
    exit();
}