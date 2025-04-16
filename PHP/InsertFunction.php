<?php
require_once 'dbConnection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get all POST values
    $product_name     = $_POST['productName'];
    $category_id      = $_POST['productCategory'];
    $size             = $_POST['productSize'];
    $color            = $_POST['productColor'];
    $price            = $_POST['productPrice'];
    $stock_quantity   = $_POST['productStock'];
    $description      = $_POST['productDescription'];
    $image_url        = $_POST['productImage'];

    // Call the insert function
    $result = Insert($product_name, $category_id, $size, $color, $price, $stock_quantity, $description, $image_url);

    echo $result; // JSON response already built in function
    exit;
}

function Insert($product_name, $category_id, $size, $color, $price, $stock_quantity, $description, $image_url) {
    try {
        $db = new Database();
        $conn = $db->getConnection();

        if ($conn === null) {
            throw new Exception("Database connection failed.");
        }

        $stmt = $conn->prepare("CALL InsertProduct(:p_name, :p_category_id, :p_size, :p_color, :p_price, :p_stock_quantity, :p_description, :p_image_url)");

        $stmt->execute([
            ':p_name'            => $product_name,
            ':p_category_id'     => $category_id,
            ':p_size'            => $size,
            ':p_color'           => $color,
            ':p_price'           => $price,
            ':p_stock_quantity'  => $stock_quantity,
            ':p_description'     => $description,
            ':p_image_url'       => $image_url
        ]);


        return json_encode([
            'success' => true,
            'message' => '✅ Product inserted successfully.'
        ]);
    } catch (PDOException $e) {
        return json_encode([
            'success' => false,
            'message' => '❌ Database Error: ' . $e->getMessage()
        ]);
    } catch (Exception $e) {
        return json_encode([
            'success' => false,
            'message' => '❌ Error: ' . $e->getMessage()
        ]);
    }
}
?>
