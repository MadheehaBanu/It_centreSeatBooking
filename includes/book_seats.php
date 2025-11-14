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

// Check if user already has a booking (one booking per user rule)
$sql_user_check = "SELECT seat_number FROM bookings WHERE user_id = ?";
$stmt_user_check = $conn->prepare($sql_user_check);
$stmt_user_check->bind_param("i", $user_id);
$stmt_user_check->execute();
$result_user_check = $stmt_user_check->get_result();

if ($result_user_check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'You already have a booking. Each user can only book one seat.']);
    exit();
}

// Only allow booking of one seat at a time
if (count($selected_seats) > 1) {
    echo json_encode(['success' => false, 'message' => 'You can only book one seat at a time.']);
    exit();
}

$placeholders = implode(',', array_fill(0, count($selected_seats), '?'));
$types = str_repeat('i', count($selected_seats));

// Check if any of the selected seats are already booked
$sql_check = "SELECT seat_number FROM bookings WHERE seat_number IN ($placeholders)";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param($types, ...$selected_seats);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Selected seat is already booked.']);
    exit();
}

$sql = "INSERT INTO bookings (user_id, seat_number, status) VALUES (?, ?, 'pending')";
$stmt = $conn->prepare($sql);

$conn->begin_transaction();
foreach ($selected_seats as $seat) {
    $stmt->bind_param("ii", $user_id, $seat);
    if (!$stmt->execute()) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Booking failed. Please try again.']);
        exit();
    }
}

$conn->commit();
$booking_time = date('Y-m-d H:i:s');
echo json_encode([
    'success' => true,
    'seats' => $selected_seats,
    'booking_time' => $booking_time
]);

$stmt->close();
$conn->close();
?> 