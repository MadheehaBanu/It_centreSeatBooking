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
$stmt = $conn->prepare("SELECT first_name, last_name, registration_number, email, phone_number, created_at FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;

$initials = $user ? strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) : '?';
$full_name = $user ? htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - UniSpace</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <div class="main-container">

        <!-- Header -->
        <div class="profile-header">
            <div class="header-left">
                <img src="images/logo-main.png" alt="Logo" class="logo-header">
                <span class="unispace-title">UniSpace</span>
            </div>
            <nav class="header-nav">
                <a href="status.php" class="nav-link">My Booking</a>
                <a href="booking.php" class="nav-link">Book a Seat</a>
            </nav>
        </div>

        <div class="profile-content">
            <?php if ($user): ?>
                <!-- Avatar -->
                <div class="profile-avatar">
                    <div class="avatar-circle">
                        <span class="avatar-initials"><?php echo $initials; ?></span>
                    </div>
                    <div class="avatar-info">
                        <p class="avatar-name"><?php echo $full_name; ?></p>
                        <p class="avatar-email"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>

                <!-- Fields -->
                <div class="profile-details">
                    <?php
                    $fields = [
                        'First Name'          => $user['first_name'],
                        'Last Name'           => $user['last_name'],
                        'Registration Number' => $user['registration_number'],
                        'Email'               => $user['email'],
                        'Phone Number'        => $user['phone_number'],
                        'Member Since'        => date('F d, Y', strtotime($user['created_at'])),
                    ];
                    foreach ($fields as $label => $value): ?>
                        <div class="detail-group">
                            <span class="detail-label"><?php echo $label; ?></span>
                            <span class="detail-value"><?php echo htmlspecialchars($value); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <div class="error-state">
                    <p class="error-icon">⚠</p>
                    <h2>User not found</h2>
                    <p>Unable to load your profile. Please try logging in again.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Actions -->
        <div class="profile-actions">
            <button id="back-btn" class="btn-back">← Back</button>
            <button id="logout-btn" class="btn-logout">Logout</button>
        </div>

    </div>

    <script src="js/profile.js"></script>
</body>
</html>
