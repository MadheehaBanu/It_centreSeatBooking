<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header('HTTP/1.1 403 Forbidden');
    exit('Unauthorized access');
}

// Get all bookings with user details
$sql = "SELECT b.id, b.seat_number, b.booking_time, b.status, 
               u.first_name, u.last_name, u.email, u.registration_number 
        FROM bookings b 
        JOIN users u ON b.user_id = u.id 
        ORDER BY b.booking_time DESC";
$result = $conn->query($sql);

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="unispace_bookings_' . date('Y-m-d_H-i-s') . '.csv"');

// Create file pointer
$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, [
    'Booking ID',
    'First Name',
    'Last Name',
    'Email',
    'Registration Number',
    'Seat Number',
    'Booking Time',
    'Status'
]);

// Add data rows
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['id'],
            $row['first_name'],
            $row['last_name'],
            $row['email'],
            $row['registration_number'],
            $row['seat_number'],
            $row['booking_time'],
            ucfirst($row['status'])
        ]);
    }
}

fclose($output);
$conn->close();
?>
