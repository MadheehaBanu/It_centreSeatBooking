<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'];

if ($action === 'approve_all') {
    $sql = "UPDATE bookings SET status = 'approved' WHERE status = 'pending'";
} elseif ($action === 'reject_all') {
    $sql = "UPDATE bookings SET status = 'rejected' WHERE status = 'pending'";
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
    exit();
}

if ($conn->query($sql)) {
    $affected_rows = $conn->affected_rows;
    echo json_encode(['success' => true, 'message' => "Updated {$affected_rows} bookings."]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to perform bulk action.']);
}

$conn->close();
?>
