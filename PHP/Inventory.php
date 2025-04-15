<?php
require_once '../PHP/dbConnection.php';
require_once '../PHP/crud.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_name = $_POST['productName'];
    $category_id = $_POST['productCategory'];
    $size = $_POST['productSize'] ?? 'M';
    $color = $_POST['productColor'] ?? 'Black';
    $price = $_POST['productPrice'] ?? 0;
    $stock_quantity = $_POST['productStock'] ?? 0;
    $description = $_POST['productDescription'] ?? '';
    $image_url = $_POST['productImage'];

    $crud = new Crud(); 
    $crud->InsertProducts($product_name, $category_id, $size, $color, $price, $stock_quantity, $description, $image_url);
    exit();
}
?>
