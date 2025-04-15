<?php
class Crud {
    private $conn;

    public function __construct() {
        try {
            // Update with your real DB credentials
            $host = "localhost";
            $db_name = "e-shopmanage"; 
            $username = "root"; 
            $password = ""; 

            $this->conn = new PDO("mysql:host=$host;dbname=$ e-shopmanage;charset=utf8", $root, $pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    public function InsertProducts($product_name, $category_id, $size, $color, $price, $stock_quantity, $description, $image_url) {
        try {
            $sql = "INSERT INTO products (pname, pcategory, psize, pcolor, pprice, pstock, pdesc, pimage)
                    VALUES (:product_name, :category_id, :size, :color, :price, :stock_quantity, :description, :image_url)";
            
            $stmt = $this->conn->prepare($sql);

            $stmt->execute([
                ':product_name' => $product_name,
                ':category_id' => $category_id,
                ':size' => $size,
                ':color' => $color,
                ':price' => $price,
                ':stock_quantity' => $stock_quantity,
                ':description' => $description,
                ':image_url' => $image_url
            ]);

            return true;
        } catch (PDOException $e) {
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
}
?>
