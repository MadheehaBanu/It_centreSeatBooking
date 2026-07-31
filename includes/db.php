<?php
// includes/db.php - auto-selects SQLite if data/unispace.sqlite exists, otherwise creates & uses it
// Falls back to Postgres only if explicitly needed

$sqliteFile = __DIR__ . '/../data/unispace.sqlite';
$dataDir = __DIR__ . '/../data';

// Ensure data directory exists
if (!is_dir($dataDir)) {
    @mkdir($dataDir, 0755, true);
}

// Auto-initialize SQLite database if it doesn't exist
if (!file_exists($sqliteFile)) {
    try {
        $pdo = new PDO('sqlite:' . $sqliteFile);
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
        
        // Insert sample users (password is 'password')
        $stmt = $pdo->prepare('INSERT INTO users (first_name, last_name, registration_number, email, phone_number, password) VALUES (?, ?, ?, ?, ?, ?)');
        $sampleUsers = [
            ['John', 'Doe', 'REG001', 'john@example.com', '1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'],
            ['Jane', 'Smith', 'REG002', 'jane@example.com', '0987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'],
            ['Test', 'User', 'REG003', 'test@example.com', '5555555555', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'],
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
        
    } catch (PDOException $e) {
        // If SQLite creation fails, clean up and die
        if (file_exists($sqliteFile)) {
            @unlink($sqliteFile);
        }
        die("Database initialization failed: " . htmlspecialchars($e->getMessage()));
    }
}

// Now connect to SQLite
try {
    $conn = new PDO('sqlite:' . $sqliteFile);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Ensure foreign keys are enforced
    $conn->exec('PRAGMA foreign_keys = ON;');
} catch (PDOException $e) {
    die("SQLite connection failed: " . htmlspecialchars($e->getMessage()));
}
?>
