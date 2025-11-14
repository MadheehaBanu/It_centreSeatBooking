-- UniSpace Complete Database Setup
-- This file creates the complete database with all tables and features

-- Create the database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `unispace_db`;
USE `unispace_db`;

-- Users table for storing user information
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `registration_number` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `registration_number` (`registration_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Bookings table for storing workspace reservations
CREATE TABLE IF NOT EXISTS `bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `seat_number` int(11) NOT NULL,
  `booking_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample data for testing (optional)
-- You can remove these INSERT statements if you don't want sample data

-- Sample users
INSERT INTO `users` (`first_name`, `last_name`, `registration_number`, `email`, `phone_number`, `password`) VALUES
('John', 'Doe', 'REG001', 'john@example.com', '1234567890', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Jane', 'Smith', 'REG002', 'jane@example.com', '0987654321', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Test', 'User', 'REG003', 'test@example.com', '5555555555', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Sample bookings with different statuses
INSERT INTO `bookings` (`user_id`, `seat_number`, `status`) VALUES
(1, 12, 'approved'),
(2, 25, 'pending'),
(3, 33, 'rejected');

-- Display success message
SELECT 'UniSpace database setup completed successfully!' as 'Status';
