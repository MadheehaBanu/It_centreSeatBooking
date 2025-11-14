<?php
require 'db.php';

$sql = "SELECT seat_number FROM bookings WHERE status = 'approved'";
$result = $conn->query($sql);

$booked_seats = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $booked_seats[] = (int)$row['seat_number'];
    }
}

$conn->close();

header('Content-Type: application/json');
echo json_encode($booked_seats);
?> 