<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// If user is admin, redirect to admin panel
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header("Location: admin.php");
    exit();
}

// Get user's booking information
require 'includes/db.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT seat_number, booking_time, status FROM bookings WHERE user_id = ? ORDER BY booking_time DESC LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$booking = null;
if ($row = $result->fetch_assoc()) {
    $booking = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Status - UniSpace</title>
    <link rel="stylesheet" href="css/status.css">
</head>
<body>
    <div class="main-container">
        <div class="status-header">
            <div class="header-left">
                <img src="images/logo-main.png" alt="Logo" class="logo-header">
                <span class="unispace-title">UniSpace</span>
            </div>
            <div class="profile-icon" onclick="window.location.href='profile.php'"></div>
        </div>
        
        <div class="status-content">
            <?php if ($booking): ?>
                <div class="booking-status">
                    <h2>Booking Confirmed!</h2>
                    <div class="booking-details">
                        <div class="detail-item">
                            <span class="label">Seat Number:</span>
                            <span class="value"><?php echo htmlspecialchars($booking['seat_number']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Booking Time:</span>
                            <span class="value"><?php echo date('M d, Y - h:i A', strtotime($booking['booking_time'])); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Status:</span>
                            <span class="value status-<?php echo strtolower($booking['status'] ?? 'pending'); ?>">
                                <?php echo ucfirst($booking['status'] ?? 'Pending'); ?>
                            </span>
                        </div>
                    </div>
                    
                    <?php if ($booking['status'] === 'approved'): ?>
                        <div class="qr-section">
                            <h3>Your QR Code</h3>
                            <div id="qrcode" class="qr-container"></div>
                            <p class="qr-note">Show this QR code for verification</p>
                        </div>
                    <?php elseif ($booking['status'] === 'pending'): ?>
                        <div class="pending-message">
                            <h3>Awaiting Approval</h3>
                            <p>Your booking is pending admin approval. You will be notified once it's approved.</p>
                        </div>
                    <?php elseif ($booking['status'] === 'rejected'): ?>
                        <div class="rejected-message">
                            <h3>Booking Rejected</h3>
                            <p>Unfortunately, your booking has been rejected. Please contact admin for more information.</p>
                            <a href="booking.php" class="btn-book-new">Book Another Seat</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div class="no-booking">
                    <h2>No Active Booking</h2>
                    <p>You don't have any active bookings.</p>
                    <a href="booking.php" class="btn-book-new">Book a Seat</a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="status-actions">
            <?php if ($booking && $booking['status'] !== 'rejected'): ?>
                <button id="cancel-booking-btn" class="btn-cancel-booking">Cancel Booking</button>
            <?php endif; ?>
            <button id="close-btn" class="btn-close">Close</button>
        </div>
    </div>
    
    <script src="js/qrcode.min.js"></script>
    <script src="js/status.js"></script>
</body>
</html>
