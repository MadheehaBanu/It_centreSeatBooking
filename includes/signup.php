<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $registration_number = $_POST['registration_number'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate passwords match
    if ($password !== $confirm_password) {
        header("Location: ../signup.php?error=passwordmismatch");
        exit();
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        header("Location: ../signup.php?error=emailexists");
        exit();
    }

    // Check if registration number already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE registration_number = ?");
    $stmt->execute([$registration_number]);
    if ($stmt->fetch()) {
        header("Location: ../signup.php?error=regnumberexists");
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, registration_number, email, phone_number, password) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$first_name, $last_name, $registration_number, $email, $phone_number, $hashed_password])) {
        header("Location: ../index.php?signup=success");
        exit();
    } else {
        header("Location: ../signup.php?error=sqlerror");
        exit();
    }
} else {
    header("Location: ../signup.php");
    exit();
}
?>