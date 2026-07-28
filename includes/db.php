<?php
// includes/db.php - auto-selects SQLite if data/unispace.sqlite exists, otherwise uses Postgres

$sqliteFile = __DIR__ . '/../data/unispace.sqlite';

if (file_exists($sqliteFile)) {
    try {
        $conn = new PDO('sqlite:' . $sqliteFile);
        // Better fetch mode by default
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        // Ensure foreign keys are enforced
        $conn->exec('PRAGMA foreign_keys = ON;');
    } catch (PDOException $e) {
        die("SQLite connection failed: " . $e->getMessage());
    }
} else {
    // Fallback to original Postgres config from the repo
    $host = "localhost";
    $port = "5432";
    $dbname = "unispace_db";
    $username = "postgres";
    $password = "1234";

    try {
        $conn = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>
