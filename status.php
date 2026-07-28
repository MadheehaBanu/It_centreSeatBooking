<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header("Location: admin.php");
    exit();
}

require 'includes/db.php';

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT seat_number, booking_time, status FROM bookings WHERE user_id = ? ORDER BY booking_time DESC LIMIT 1");
$stmt->execute([$user_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
$status  = $booking ? strtolower($booking['status'] ?? 'pending') : null;
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

        <!-- Header -->
        <div class="status-header">
            <div class="header-left">
                <img src="images/logo-main.png" alt="Logo" class="logo-header">
                <span class="unispace-title">UniSpace</span>
            </div>
            <a href="profile.php" class="profile-link" title="My Profile">
                <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                </svg>
            </a>
        </div>

        <!-- Toast -->
        <div id="toast" class="toast" role="alert" aria-live="polite"></div>

        <div class="status-content">
            <?php if ($booking): ?>

                <!-- Status banner -->
                <div class="status-banner status-banner-<?php echo $status; ?>">
                    <span class="status-banner-icon">
                        <?php
                        if ($status === 'approved')      echo '✓';
                        elseif ($status === 'rejected')  echo '✕';
                        else                             echo '⏳';
                        ?>
                    </span>
                    <span class="status-banner-text">
                        <?php
                        if ($status === 'approved')      echo 'Booking Approved';
                        elseif ($status === 'rejected')  echo 'Booking Rejected';
                        else                             echo 'Awaiting Approval';
                        ?>
                    </span>
                </div>

                <!-- Booking details -->
                <div class="booking-details">
                    <div class="detail-item">
                        <span class="detail-label">Seat Number</span>
                        <span class="detail-value"><?php echo htmlspecialchars($booking['seat_number']); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Booked On</span>
                        <span class="detail-value"><?php echo date('M d, Y — h:i A', strtotime($booking['booking_time'])); ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Status</span>
                        <span class="detail-value status-<?php echo $status; ?>"><?php echo ucfirst($status); ?></span>
                    </div>
                </div>

                <?php if ($status === 'approved'): ?>
                    <!-- QR section -->
                    <div class="qr-section">
                        <p class="qr-title">Your Access QR Code</p>
                        <div class="qr-card">
                            <div id="qrcode"></div>
                        </div>
                        <p class="qr-note">Show this at the entrance for verification</p>
                    </div>

                <?php elseif ($status === 'pending'): ?>
                    <div class="info-message info-pending">
                        <p class="info-message-title">Waiting for admin approval</p>
                        <p class="info-message-body">This page refreshes automatically. You'll see your QR code here once approved.</p>
                    </div>

                <?php elseif ($status === 'rejected'): ?>
                    <div class="info-message info-rejected">
                        <p class="info-message-title">Your booking was not approved</p>
                        <p class="info-message-body">Please contact the admin for more information, or book a different seat.</p>
                        <a href="booking.php" class="btn-book-new">Book Another Seat</a>
                    </div>
                <?php endif; ?>

            <?php else: ?>
                <div class="no-booking">
                    <p class="no-booking-icon">🪑</p>
                    <h2>No Active Booking</h2>
                    <p>You haven't booked a seat yet.</p>
                    <a href="booking.php" class="btn-book-new">Book a Seat</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Actions -->
        <div class="status-actions">
            <?php if ($booking && $status !== 'rejected'): ?>
                <button id="cancel-booking-btn" class="btn-cancel-booking">Cancel Booking</button>
            <?php endif; ?>
            <a href="booking.php" class="btn-back">← Back to Seats</a>
        </div>

    </div>

    <script src="js/qrcode.min.js"></script>
    <script src="js/status.js"></script>
</body>
</html>
