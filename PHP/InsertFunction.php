<?php
require_once 'dbConnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get all POST values
    $product_name     = $_POST['productName'] ?? '';
    $category_id      = $_POST['productCategory'] ?? '';
    $size             = $_POST['productSize'] ?? '';
    $color            = $_POST['productColor'] ?? '';
    $price            = $_POST['productPrice'] ?? '';
    $stock_quantity   = $_POST['productStock'] ?? '';
    $description      = $_POST['productDescription'] ?? '';

    // Handle image URL
    $image_url = '';
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/';
        $file_name = time() . '_' . basename($_FILES['productImage']['name']);
        $target_file = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['productImage']['tmp_name'], $target_file)) {
            $image_url = 'uploads/' . $file_name;
        }
    } else if (isset($_POST['productImage']) && !empty($_POST['productImage'])) {
        $image_url = $_POST['productImage'];
    }

    // Call the insert function
    $result = Insert($product_name, $category_id, $size, $color, $price, $stock_quantity, $description, $image_url);

    // Set JSON header and return clean response
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}

function Insert($product_name, $category_id, $size, $color, $price, $stock_quantity, $description, $image_url) {
    try {
        $db = new Database();
        $conn = $db->getConnection();

        if ($conn === null) {
            throw new Exception("Database connection failed.");
        }

        // Prepare CALL to InsertProducts
        $stmt = $conn->prepare("CALL InsertProducts(:p_name, :p_category_id, :p_size, :p_color, :p_price, :p_stock_quantity, :p_description, :p_image_url)");
        $stmt->execute([
            ':p_name' => $product_name,
            ':p_category_id' => $category_id,
            ':p_size' => $size,
            ':p_color' => $color,
            ':p_price' => $price,
            ':p_stock_quantity' => $stock_quantity,
            ':p_description' => $description,
            ':p_image_url' => $image_url
        ]);

        // Get last inserted ID
        // 🔥 Immediately fetch inserted product INSIDE the stored procedure
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$product) {
            // No product fetched, but insert might still have succeeded
            return [
                'success' => true,
                'message' => '✅ Product inserted successfully (no product returned).'
            ];
        }
        return [
            'success' => true,
            'product' => $product,
            'message' => '✅ Product inserted successfully.'
        ];
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => '❌ Database Error: ' . $e->getMessage()
        ];
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage());
        return [
            'success' => false,
            'message' => '❌ Error: ' . $e->getMessage()
        ];
    }
}

