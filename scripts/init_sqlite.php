<?php
// scripts/init_sqlite.php
// Run: php scripts/init_sqlite.php (from repository root)
// This script creates data/unispace.sqlite and populates it with tables and sample data.

$dir = __DIR__ . '/../data';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}
$dbFile = $dir . '/unispace.sqlite';

// If DB exists, warn and exit
if (file_exists($dbFile)) {
    echo "Database already exists at $dbFile\n";
    exit(0);
}

try {
    $pdo = new PDO('sqlite:' . $dbFile);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Enable foreign keys
    $pdo->exec('PRAGMA foreign_keys = ON;');

    // Create users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            first_name TEXT NOT NULL,
            last_name TEXT NOT NULL,
            registration_number TEXT NOT NULL UNIQUE,
            email TEXT NOT NULL UNIQUE,
            phone_number TEXT NOT NULL,
            password TEXT NOT NULL,
            created_at DATETIME NOT NULL DEFAULT (datetime('now'))
        );");

    // Create bookings table
    $pdo->exec("CREATE TABLE IF NOT EXISTS bookings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            seat_number INTEGER NOT NULL,
            booking_time DATETIME NOT NULL DEFAULT (datetime('now')),
            status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending','approved','rejected')),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );");

    // Insert sample users (passwords copied from original SQL)
    $stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, registration_number, email, phone_number, password) VALUES (?, ?, ?, ?, ?, ?)');
    $sampleUsers = [
        ['John','Doe','REG001','john@example.com','1234567890','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'],
        ['Jane','Smith','REG002','jane@example.com','0987654321','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'],
        ['Test','User','REG003','test@example.com','5555555555','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'],
    ];
    foreach ($sampleUsers as $u) {
        $stmt->execute($u);
    }

    // Insert sample bookings
    $stmt2 = $pdo->prepare('INSERT INTO bookings (user_id, seat_number, status) VALUES (?, ?, ?)');
    $sampleBookings = [
        [1, 12, 'approved'],
        [2, 25, 'pending'],
        [3, 33, 'rejected'],
    ];
    foreach ($sampleBookings as $b) {
        $stmt2->execute($b);
    }

    echo "SQLite database created at: $dbFile\n";
    echo "You can now run the app using PHP built-in server (see README changes).\n";
} catch (PDOException $e) {
    echo "Error creating SQLite DB: " . $e->getMessage() . "\n";
    if (file_exists($dbFile)) {
        unlink($dbFile);
    }
}
