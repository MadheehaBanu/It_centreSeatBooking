<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$booking_id = $data['booking_id'];
$status = $data['status'];

// Validate status
if (!in_array($status, ['pending', 'approved', 'rejected'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid status.']);
    exit();
}

$sql = "UPDATE bookings SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $status, $booking_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Status updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
}

$stmt->close();
$conn->close();
?>
