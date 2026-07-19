<?php
require 'db.php';

$stmt = $conn->query("SELECT seat_number FROM bookings WHERE status = 'approved'");
$booked_seats = array_map(fn($row) => (int)$row['seat_number'], $stmt->fetchAll(PDO::FETCH_ASSOC));

header('Content-Type: application/json');
echo json_encode($booked_seats);
