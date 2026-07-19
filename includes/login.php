<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if fields are empty
    if(empty($_POST['email']) || empty($_POST['password'])) {
        header("Location: ../index.php?error=emptyfields");
        exit();
    }
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check for admin login first
    if ($email === 'admin@gmail.com' && $password === 'Admin@123') {
        $_SESSION['user_id'] = 'admin';
        $_SESSION['email'] = $email;
        $_SESSION['is_admin'] = true;
        header("Location: ../admin.php");
        exit();
    }

    $sql = "SELECT id, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($password, $row['password'])) {
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['email'] = $email;
        $_SESSION['is_admin'] = false;

        $booking_stmt = $conn->prepare("SELECT id FROM bookings WHERE user_id = ? LIMIT 1");
        $booking_stmt->execute([$row['id']]);
        if ($booking_stmt->fetch()) {
            header("Location: ../status.php");
        } else {
            header("Location: ../booking.php");
        }
        exit();
    } else {
        header("Location: ../index.php?error=wrongcredentials");
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>