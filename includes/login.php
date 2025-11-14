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
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $email;
            $_SESSION['is_admin'] = false;
            
            // Check if user has existing booking and redirect accordingly
            $booking_sql = "SELECT id FROM bookings WHERE user_id = ? LIMIT 1";
            $booking_stmt = $conn->prepare($booking_sql);
            $booking_stmt->bind_param("i", $row['id']);
            $booking_stmt->execute();
            $booking_result = $booking_stmt->get_result();
            
            if ($booking_result->num_rows > 0) {
                header("Location: ../status.php");
            } else {
                header("Location: ../booking.php");
            }
            $booking_stmt->close();
            $stmt->close();
            exit();
        } else {
            header("Location: ../index.php?error=wrongcredentials");
            $stmt->close();
            $conn->close();
            exit();
        }
    } else {
        header("Location: ../index.php?error=wrongcredentials");
        $stmt->close();
        $conn->close();
        exit();
    }
} else {
    header("Location: ../index.php");
    exit();
}
?>