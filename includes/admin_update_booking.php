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

if ($seat_number < 1 || $seat_number > 40) {
    echo json_encode(['success' => false, 'message' => 'Invalid seat number.']);
    exit();
}

$check_stmt = $conn->prepare("SELECT id FROM bookings WHERE seat_number = ? AND id != ? AND status != 'rejected'");
$check_stmt->execute([$seat_number, $booking_id]);
if ($check_stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Seat is already booked by another user.']);
    exit();
}

$formatted_time = date('Y-m-d H:i:s', strtotime($booking_time));
$stmt = $conn->prepare("UPDATE bookings SET seat_number = ?, booking_time = ? WHERE id = ?");

if ($stmt->execute([$seat_number, $formatted_time, $booking_id])) {
    echo json_encode(['success' => true, 'message' => 'Booking updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update booking.']);
}
