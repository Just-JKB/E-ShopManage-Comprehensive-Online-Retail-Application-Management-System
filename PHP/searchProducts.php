<?php
require_once 'dbConnection.php';

// Get the search query and category filter from the request
$searchQuery = isset($_POST['searchQuery']) ? $_POST['searchQuery'] : '';
$categoryFilter = isset($_POST['categoryFilter']) ? $_POST['categoryFilter'] : 'all';

try {
    // Database connection
    $db = new Database();
    $conn = $db->getConnection();

    // Prepare the stored procedure call
    $stmt = $conn->prepare("CALL searchProducts(:searchQuery, :categoryFilter)");
    $stmt->bindParam(':searchQuery', $searchQuery, PDO::PARAM_STR);
    $stmt->bindParam(':categoryFilter', $categoryFilter, PDO::PARAM_STR);

    // Execute the stored procedure
    $stmt->execute();

    // Fetch results
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return the results as JSON
    echo json_encode(['success' => true, 'products' => $products]);
} catch (Exception $e) {
    // Return an error response if something goes wrong
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
