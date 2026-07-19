<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$stmt = $conn->prepare("DELETE FROM bookings WHERE user_id = ?");
if ($stmt->execute([$_SESSION['user_id']])) {
    echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to cancel booking.']);
}
