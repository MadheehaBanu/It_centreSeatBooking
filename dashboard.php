<?php
session_start();

// If not logged in, send to login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Admin users are sent to admin panel
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true) {
    header('Location: admin.php');
    exit();
}

$userEmail = $_SESSION['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - UniSpace</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="dashboard-box">
            <img src="images/logo-main.png" alt="Logo" style="max-width:180px;">
            <h2>Welcome to your UniSpace Dashboard</h2>
            <?php if ($userEmail): ?>
                <p>Signed in as <strong><?php echo htmlspecialchars($userEmail); ?></strong></p>
            <?php endif; ?>

            <ul class="dashboard-links">
                <li><a href="profile.php">My Profile</a></li>
                <li><a href="booking.php">Book a Seat</a></li>
                <li><a href="status.php">My Booking Status</a></li>
                <li><a href="includes/logout.php">Sign out</a></li>
            </ul>
        </div>
    </div>
</body>
</html>
