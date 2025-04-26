<?php
require_once 'dbConnection.php';
require_once 'InsertFunction.php'; // Optional if Insert() is used elsewhere

// Create database connection
$db = new Database();
$conn = $db->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Extract POST values safely
    $product_name    = htmlspecialchars($_POST["name"]);
    $category_id     = htmlspecialchars($_POST["category"]);
    $size            = $_POST["size"] ?? '';
    $color           = $_POST["color"] ?? '';
    $price           = floatval($_POST["price"]);
    $stock_quantity  = $_POST["stock_quantity"] ?? 0;
    $description     = htmlspecialchars($_POST["description"]);
    $image_url       = htmlspecialchars($_POST["image_url"]);

    // Prepare insert query
    $stmt = $conn->prepare("INSERT INTO products (product_name, category_id, size, color, price, stock_quantity, description, image_url) 
    VALUES (:product_name, :category_id, :size, :color, :price, :stock_quantity, :description, :image_url)");

    // Bind parameters
    $stmt->bindParam(':product_name', $product_name);
    $stmt->bindParam(':category_id', $category_id);
    $stmt->bindParam(':size', $size);
    $stmt->bindParam(':color', $color);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':stock_quantity', $stock_quantity);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':image_url', $image_url);

    if ($stmt->execute()) {
        // Redirect to the HTML page with success
        header("Location: ../HTML/Inventory.html?success=1");
    } else {
        echo "Error inserting product: " . $stmt->errorInfo()[2];
    }

    $conn = null;
    exit;
}

// GET method for fetching products
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    header('Content-Type: application/json');

    $result = $conn->query("SELECT * FROM products");
    $products = [];

    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $products[] = $row;
    }

    echo json_encode($products);
    $conn = null;
    exit;
}
?>
