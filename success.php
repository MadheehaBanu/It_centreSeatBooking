<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Successful - UniSpace</title>
    <link rel="stylesheet" href="css/success.css">
</head>
<body>
    <div class="success-container">
        <div class="success-card">
            <div class="icon-container">
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
            </div>
            <h2>Booking Successful</h2>
            <button class="btn-qr">
                <img src="images/qr-code.png" alt="QR Code Icon">
                Generate QR
            </button>
        </div>
    </div>
</body>
</html> 