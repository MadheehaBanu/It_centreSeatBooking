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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Workspace - UniSpace</title>
    <link rel="stylesheet" href="css/booking.css">
</head>
<body>
    <div class="main-container">
        <div class="booking-header">
            <div class="header-left">
                <img src="images/logo-main.png" alt="Logo" class="logo-header">
                <span class="unispace-title">UniSpace</span>
            </div>
            <div class="profile-icon" onclick="window.location.href='profile.php'"></div>
        </div>
        <div class="seat-area">
            <div class="seat-grid">
                <?php
                $rows = [
                    [1,2,3,4,5,6,7,8,9,10],
                    [11, 12,13,14,15,16,17,18,19,20, 21],
                    [22, 23,24,25,26,27,28,29,30, 31],
                    [32,33,34,35,36,37,38,39, 40]
                ];
                foreach ($rows as $rowIndex => $row) {
                    echo '<div class="seat-row row-' . ($rowIndex + 1) . '">';
                    foreach ($row as $seat) {
                        echo '<div class="seat" data-seat-number="'.$seat.'">'.$seat.'</div>';
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </div>
        <div class="legend-actions">
            <div class="legend">
                <div class="legend-item"><span class="legend-dot available"></span>Available</div>
                <div class="legend-item"><span class="legend-dot booked"></span>Booked</div>
            </div>
            <div class="actions">
                <button class="btn-book">Book Now</button>
                <button class="btn-cancel">Cancel</button>
            </div>
        </div>
    </div>
    <script src="js/qrcode.min.js"></script>
    <script src="js/booking.js"></script>
</body>
</html> 