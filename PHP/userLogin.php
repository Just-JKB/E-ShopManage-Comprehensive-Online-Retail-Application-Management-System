<?php
session_start();
require_once 'dbConnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Connect to database
    $database = new Database();
    $conn = $database->getConnection();

    // Check user credentials
    $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];

            // Redirect to user dashboard
            header("Location: ../PHP/UserDashboard.php");
            exit();
        } else {
            // Incorrect password
            showAlert("Login Failed", "Invalid password. Please try again.");
        }
    } else {
        // No user found with that email
        showAlert("Login Failed", "No user found with that email. Please check or register.");
    }
}

// Function to show SweetAlert error
function showAlert($title, $message) {
    echo <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <title>Login Error</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: "error",
                    title: "$title",
                    text: "$message",
                    confirmButtonColor: "#3085d6"
                }).then(() => {
                    window.location.href = "../PHP/userLogin.php";
                });
            });
        </script>
    </body>
    </html>
    HTML;
    exit();
}
?>
