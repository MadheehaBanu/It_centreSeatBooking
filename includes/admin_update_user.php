<?php
session_start();
require_once 'db.php';

// Check if user is admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'] ?? '';
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $registration_number = trim($_POST['registration_number'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone_number = trim($_POST['phone_number'] ?? '');
    
    if (empty($user_id) || empty($first_name) || empty($last_name) || 
        empty($registration_number) || empty($email) || empty($phone_number)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format']);
        exit();
    }
    
    try {
        // Check if email or registration number already exists for other users
        $check_sql = "SELECT id FROM users WHERE (email = ? OR registration_number = ?) AND id != ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ssi", $email, $registration_number, $user_id);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Email or registration number already exists for another user']);
            exit();
        }
        
        // Update user details
        $update_sql = "UPDATE users SET first_name = ?, last_name = ?, registration_number = ?, email = ?, phone_number = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssssi", $first_name, $last_name, $registration_number, $email, $phone_number, $user_id);
        
        if ($update_stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'User details updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update user details']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }
    
} else {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
