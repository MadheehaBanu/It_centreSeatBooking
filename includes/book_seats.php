<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$selected_seats = $data['seats'];

if (empty($selected_seats)) {
    echo json_encode(['success' => false, 'message' => 'No seats selected.']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if user already has a booking
$stmt = $conn->prepare("SELECT seat_number FROM bookings WHERE user_id = ?");
$stmt->execute([$user_id]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'You already have a booking. Each user can only book one seat.']);
    exit();
}

// Only allow booking of one seat at a time
if (count($selected_seats) > 1) {
    echo json_encode(['success' => false, 'message' => 'You can only book one seat at a time.']);
    exit();
}

$placeholders = implode(',', array_fill(0, count($selected_seats), '?'));

// Check if any of the selected seats are already booked
$stmt = $conn->prepare("SELECT seat_number FROM bookings WHERE seat_number IN ($placeholders)");
$stmt->execute($selected_seats);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Selected seat is already booked.']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO bookings (user_id, seat_number, status) VALUES (?, ?, 'pending')");

try {
    $conn->beginTransaction();
    foreach ($selected_seats as $seat) {
        $stmt->execute([$user_id, $seat]);
    }
    $conn->commit();
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => 'Booking failed. Please try again.']);
    exit();
}

$booking_time = date('Y-m-d H:i:s');
echo json_encode([
    'success' => true,
    'seats' => $selected_seats,
    'booking_time' => $booking_time
]);
