<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];
$sql = "SELECT seat_number, booking_time, status FROM bookings WHERE user_id = ? ORDER BY booking_time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$bookings = [];
while ($row = $result->fetch_assoc()) {
    $bookings[] = [
        'seat_number' => $row['seat_number'],
        'booking_time' => $row['booking_time'],
        'status' => $row['status']
    ];
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($bookings); 