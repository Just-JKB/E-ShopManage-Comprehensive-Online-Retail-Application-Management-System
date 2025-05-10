<?php
require_once 'dbConnection.php';

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $contact_number = $_POST['contact_number'];
    $address = $_POST['address'];

    // Contact number length validation
    if (strlen($contact_number) > 11) {
        echo '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Invalid Contact Number</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "error",
                        title: "Invalid Contact Number",
                        text: "Contact number should not exceed 11 digits.",
                        confirmButtonColor: "#3085d6"
                    }).then(() => {
                        window.location.href = "../PHP/registerUser.php";
                    });
                });
            </script>
        </body>
        </html>';
        exit();
    }

    // Check if the email already exists
    $checkEmail = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $checkEmail->execute([$email]);
    if ($checkEmail->rowCount() > 0) {
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
                    }).then(() => {
                        window.location.href = "../PHP/registerUser.php";
                    });
                });
            </script>
        </body>
        </html>';
        exit();
    }

    // Check if contact number already exists
    $checkContact = $conn->prepare("SELECT * FROM users WHERE contact_number = ?");
    $checkContact->execute([$contact_number]);
    if ($checkContact->rowCount() > 0) {
        echo '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Duplicate Contact Number</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "error",
                        title: "Duplicate Contact Number",
                        text: "This contact number is already registered.",
                        confirmButtonColor: "#3085d6"
                    }).then(() => {
                        window.location.href = "../PHP/registerUser.php";
                    });
                });
            </script>
        </body>
        </html>';
        exit();
    }

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, contact_number, address) VALUES (?, ?, ?, ?, ?)");
    $success = $stmt->execute([$name, $email, $password, $contact_number, $address]);

    if ($success) {
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
                        text: "Your account has been created!",
                        confirmButtonColor: "#3085d6"
                    }).then(() => {
                        window.location.href = "../HTML/userLogin.html";
                    });
                });
            </script>
        </body>
        </html>';
    } else {
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
                        text: "There was an error creating the user account. Please try again.",
                        confirmButtonColor: "#3085d6"
                    }).then(() => {
                        window.location.href = "../PHP/registerUser.php";
                    });
                });
            </script>
        </body>
        </html>';
    }
    exit();
}
?>
