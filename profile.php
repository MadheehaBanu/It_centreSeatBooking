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

// Get user details
require 'includes/db.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT first_name, last_name, registration_number, email, phone_number, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$user = null;
if ($row = $result->fetch_assoc()) {
    $user = $row;
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Info - UniSpace</title>
    <link rel="stylesheet" href="css/profile.css">
</head>
<body>
    <div class="main-container">
        <div class="profile-header">
            <div class="header-left">
                <img src="images/logo-main.png" alt="Logo" class="logo-header">
                <span class="unispace-title">UniSpace</span>
            </div>
            <div class="profile-icon-container">
                <div class="profile-icon active"></div>
            </div>
        </div>
        
        <div class="profile-content">
            <?php if ($user): ?>
                <div class="user-profile">
                    <div class="profile-avatar">
                        <div class="avatar-circle">
                            <div class="avatar-icon"></div>
                        </div>
                    </div>
                    
                    <div class="profile-details">
                        <div class="detail-group">
                            <label>First Name :</label>
                            <div class="detail-value"><?php echo htmlspecialchars($user['first_name']); ?></div>
                        </div>
                        
                        <div class="detail-group">
                            <label>Last Name :</label>
                            <div class="detail-value"><?php echo htmlspecialchars($user['last_name']); ?></div>
                        </div>
                        
                        <div class="detail-group">
                            <label>Registration Number :</label>
                            <div class="detail-value"><?php echo htmlspecialchars($user['registration_number']); ?></div>
                        </div>
                        
                        <div class="detail-group">
                            <label>Email :</label>
                            <div class="detail-value"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        
                        <div class="detail-group">
                            <label>Phone Number :</label>
                            <div class="detail-value"><?php echo htmlspecialchars($user['phone_number']); ?></div>
                        </div>
                        
                        <div class="detail-group">
                            <label>Member Since :</label>
                            <div class="detail-value"><?php echo date('M d, Y', strtotime($user['created_at'])); ?></div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="error-message">
                    <h2>User not found</h2>
                    <p>Unable to load user profile information.</p>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="profile-actions">
            <button id="back-btn" class="btn-back">Back</button>
            <button id="cancel-btn" class="btn-cancel">Cancel</button>
        </div>
    </div>
    
    <script src="js/profile.js"></script>
</body>
</html>
