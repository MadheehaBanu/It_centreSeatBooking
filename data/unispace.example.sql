-- SQLite-compatible SQL dump for UniSpace
-- Save this file as data/unispace.example.sql and run:
--   sqlite3 data/unispace.sqlite < data/unispace.example.sql

PRAGMA foreign_keys = ON;

BEGIN TRANSACTION;

CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  first_name TEXT NOT NULL,
  last_name TEXT NOT NULL,
  registration_number TEXT NOT NULL UNIQUE,
  email TEXT NOT NULL UNIQUE,
  phone_number TEXT NOT NULL,
  password TEXT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT (datetime('now'))
);

CREATE TABLE IF NOT EXISTS bookings (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  user_id INTEGER NOT NULL,
  seat_number INTEGER NOT NULL,
  booking_time DATETIME NOT NULL DEFAULT (datetime('now')),
  status TEXT NOT NULL DEFAULT 'pending' CHECK(status IN ('pending','approved','rejected')),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sample users (password is 'password' hashed with bcrypt)
INSERT INTO users (first_name, last_name, registration_number, email, phone_number, password) VALUES
('John', 'Doe', 'REG001', 'john@example.com', '1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Jane', 'Smith', 'REG002', 'jane@example.com', '0987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Test', 'User', 'REG003', 'test@example.com', '5555555555', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample bookings
INSERT INTO bookings (user_id, seat_number, status) VALUES
(1, 12, 'approved'),
(2, 25, 'pending'),
(3, 33, 'rejected');

COMMIT;
