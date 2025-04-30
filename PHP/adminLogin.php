<?php
session_start();
require_once 'dbConnection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Connect to database
    $database = new Database();
    $conn = $database->getConnection();

    // Check admin credentials
    $query = "SELECT * FROM admin WHERE email = :email LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(":email", $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check password
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_email'] = $admin['email'];

            // Redirect to dashboard
            header("Location: ../PHP/index.php");
            exit();
        } else {
            // Password doesn't match - show SweetAlert
            ?>
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
                            title: "Login Failed",
                            text: "Invalid password. Please try again.",
                            confirmButtonColor: "#3085d6"
                        }).then((result) => {
                            window.location.href = "../HTML/adminLogin.html";
                        });
                    });
                </script>
            </body>
            </html>
            <?php
            exit();
        }
    } else {
        // No admin found with that email - show SweetAlert
        ?>
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
                        title: "Login Failed",
                        text: "No admin found with that email. Please check your email or register.",
                        confirmButtonColor: "#3085d6"
                    }).then((result) => {
                        window.location.href = "../HTML/adminLogin.html";
                    });
                });
            </script>
        </body>
        </html>
        <?php
        exit();
    }
}
?>