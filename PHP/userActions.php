<?php
require_once 'dbConnection.php';

// Set headers
header('Content-Type: application/json');

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// Get action and user ID
$action = $_POST['action'] ?? '';
$userId = (int)($_POST['user_id'] ?? 0);

// Validate inputs
if (empty($action) || $userId <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Missing required parameters'
    ]);
    exit;
}

try {
    // Create database connection
    $db = new Database();
    $conn = $db->getConnection();
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    // Perform the requested action
    switch ($action) {
        case 'delete':
            // Delete user
            $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ?");
            $result = $stmt->execute([$userId]);
            
            if ($result && $stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User deleted successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'User not found or already deleted'
                ]);
            }
            break;
            
        case 'ban':
            // Ban user (set status to 'banned')
            $stmt = $conn->prepare("UPDATE users SET status = 'banned' WHERE user_id = ?");
            $result = $stmt->execute([$userId]);
            
            if ($result && $stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User banned successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'User not found or already banned'
                ]);
            }
            break;
            
        case 'unban':
            // Unban user (set status to 'active')
            $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE user_id = ?");
            $result = $stmt->execute([$userId]);
            
            if ($result && $stmt->rowCount() > 0) {
                echo json_encode([
                    'success' => true,
                    'message' => 'User unbanned successfully'
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'User not found or already active'
                ]);
            }
            break;
            
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Invalid action'
            ]);
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
} catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}
