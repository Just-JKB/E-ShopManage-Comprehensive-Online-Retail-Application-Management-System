<?php
require_once 'dbConnection.php';
$db = new Database();
$conn = $db->getConnection();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("CALL UpdateProduct(?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        $_POST['productId'],
        $_POST['productName'],
        $_POST['productCategory'],
        $_POST['productSize'],
        $_POST['productColor'],
        $_POST['productPrice'],
        $_POST['productStock'],
        $_POST['productDescription'],
        'images/' . $_FILES['productImage']['name'] // Update image if needed
    ]);

    // Move uploaded image if present
    if (!empty($_FILES['productImage']['name'])) {
        move_uploaded_file($_FILES['productImage']['tmp_name'], '../images/' . $_FILES['productImage']['name']);
    }

    header('Location: ../PHP/ProductManagement.php'); // Redirect after update
    exit();
}
?>