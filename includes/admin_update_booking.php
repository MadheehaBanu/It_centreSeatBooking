<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$booking_id = $data['booking_id'];
$seat_number = $data['seat_number'];
$booking_time = $data['booking_time'];

// Validate seat number
if ($seat_number < 1 || $seat_number > 40) {
    echo json_encode(['success' => false, 'message' => 'Invalid seat number.']);
    exit();
}

// Check if the new seat is already booked by someone else
$check_sql = "SELECT id FROM bookings WHERE seat_number = ? AND id != ? AND status != 'rejected'";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $seat_number, $booking_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Seat is already booked by another user.']);
    exit();
}

// Convert datetime-local format to MySQL datetime
$formatted_time = date('Y-m-d H:i:s', strtotime($booking_time));

$sql = "UPDATE bookings SET seat_number = ?, booking_time = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isi", $seat_number, $formatted_time, $booking_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Booking updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update booking.']);
}

$stmt->close();
$check_stmt->close();
$conn->close();
?>
