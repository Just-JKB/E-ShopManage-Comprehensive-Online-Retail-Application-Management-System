<?php
require_once 'dbConnection.php';
$db = new Database();
$conn = $db->getConnection();

// Get the current product image from the database
$productId = $_POST['productId'];
$query = $conn->prepare("SELECT image_url FROM products WHERE product_id = ?");
$query->execute([$productId]);
$currentImage = $query->fetchColumn();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure that categoryId is present in the form
    if (!isset($_POST['categoryId'])) {
        die('Category ID is missing.');
    }

    // Get category_id from the form
    $categoryId = $_POST['categoryId'];

    // Validate if the category_id exists in the category table
    $categoryCheck = $conn->prepare("SELECT COUNT(*) FROM categories WHERE category_id = ?");
    $categoryCheck->execute([$categoryId]);
    $categoryExists = $categoryCheck->fetchColumn();

    if (!$categoryExists) {
        echo '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Invalid Category</title>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>
        <body>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        icon: "error",
                        title: "Invalid Category",
                        text: "The selected category does not exist. Please choose a valid category.",
                        confirmButtonColor: "#3085d6"
                    }).then(() => {
                        window.location.href = "../PHP/updateProduct.php"; // Redirect to product update page
                    });
                });
            </script>
        </body>
        </html>';
        exit();
    }

    // Handle the image
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        // If a new image is uploaded, move it to the uploads directory
        $imagePath = 'uploads/' . time() . '_' . $_FILES['productImage']['name'];
        move_uploaded_file($_FILES['productImage']['tmp_name'], '../' . $imagePath);
    } else {
        // If no new image, retain the current image
        $imagePath = $currentImage;
    }

    // Prepare the SQL statement for updating the product
    $stmt = $conn->prepare("CALL UpdateProduct(?, ?, ?, ?, ?, ?, ?, ?, ?)");

    // Execute the stored procedure with the provided data
    $stmt->execute([
        $_POST['productId'],
        $_POST['productName'],
        $categoryId,  // Ensure we pass category_id instead of product_category
        $_POST['productSize'],
        $_POST['productColor'],
        $_POST['productPrice'],
        $_POST['productStock'],
        $_POST['productDescription'],
        $imagePath // Update image if needed
    ]);

    // Redirect after update
    header('Location: ../PHP/ProductManagement.php');
    exit();
}
?>
