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

        <!-- Header -->
        <div class="booking-header">
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

        <!-- Seat map -->
        <div class="seat-area">
            <p class="seat-area-label">Select your seat</p>
            <div class="seat-grid">
                <?php
                $rows = [
                    [1,2,3,4,5,6,7,8,9,10],
                    [11,12,13,14,15,16,17,18,19,20,21],
                    [22,23,24,25,26,27,28,29,30,31],
                    [32,33,34,35,36,37,38,39,40]
                ];
                foreach ($rows as $i => $row) {
                    echo '<div class="seat-row row-' . ($i + 1) . '">';
                    foreach ($row as $seat) {
                        echo '<button class="seat" data-seat-number="' . $seat . '" aria-label="Seat ' . $seat . '">' . $seat . '</button>';
                    }
                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <!-- Legend + actions -->
        <div class="legend-actions">
            <div class="legend">
                <div class="legend-item"><span class="legend-dot available"></span>Available</div>
                <div class="legend-item"><span class="legend-dot selected"></span>Selected</div>
                <div class="legend-item"><span class="legend-dot booked"></span>Booked</div>
            </div>
            <div class="actions">
                <span id="selected-label" class="selected-label" aria-live="polite"></span>
                <button class="btn-book" id="btn-book" disabled>Book Now</button>
                <button class="btn-cancel" id="btn-cancel">Clear</button>
            </div>
        </div>

    </div>
    <script src="js/booking.js"></script>
</body>
</html>
