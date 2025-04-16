<?php
require_once 'dbConnection.php';
require_once 'InsertFunction.php'; // contains your Insert() function

header('Content-Type: application/json');

// Create database connection
$db = new Database();
$conn = $db->getConnection();

// Insert new product
if (
    isset($_POST['productName'], $_POST['productCategory'], $_POST['productSize'], $_POST['productColor'],
          $_POST['productPrice'], $_POST['productStock'], $_POST['productDescription'], $_POST['productImage'])
) {
    $success = Insert(
        $_POST['productName'],
        $_POST['productCategory'],
        $_POST['productSize'],
        $_POST['productColor'],
        $_POST['productPrice'],
        $_POST['productStock'],
        $_POST['productDescription'],
        $_POST['productImage']
    );

    if ($success) {
        // Now fetch all products from the DB
        $stmt = $conn->prepare("SELECT * FROM products"); // Use your actual products table name
        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            "success" => true,
            "products" => $products
        ]);
    } else {
        echo json_encode(["success" => false, "message" => "Insert failed."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Missing fields."]);
}
