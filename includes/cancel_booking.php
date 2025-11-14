<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Delete user's booking
$sql = "DELETE FROM bookings WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to cancel booking.']);
}

$stmt->close();
$conn->close();
?>
