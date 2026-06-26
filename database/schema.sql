-- Movie Night QR Ticket System Database Schema
-- MySQL 5.7+

CREATE DATABASE IF NOT EXISTS `movie_night_db`;
USE `movie_night_db`;

-- =============================================
-- Users Table
-- =============================================
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) NOT NULL UNIQUE,
  `email` VARCHAR(255),
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_phone` (`phone`),
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Tickets Table
-- =============================================
CREATE TABLE IF NOT EXISTS `tickets` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ticket_id` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` INT NOT NULL,
  `qr_code` LONGTEXT NOT NULL,
  `qr_value` VARCHAR(255) NOT NULL UNIQUE,
  `is_used` BOOLEAN DEFAULT FALSE,
  `used_at` DATETIME,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  INDEX `idx_ticket_id` (`ticket_id`),
  INDEX `idx_qr_value` (`qr_value`),
  INDEX `idx_is_used` (`is_used`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Checkins Table (Check-in History)
-- =============================================
CREATE TABLE IF NOT EXISTS `checkins` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ticket_id` INT NOT NULL,
  `checked_in_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `scanner_ip` VARCHAR(45),
  FOREIGN KEY (`ticket_id`) REFERENCES `tickets`(`id`) ON DELETE CASCADE,
  INDEX `idx_ticket_id` (`ticket_id`),
  INDEX `idx_checked_in_at` (`checked_in_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Admin Logs Table (Optional - for audit trail)
-- =============================================
CREATE TABLE IF NOT EXISTS `admin_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `action` VARCHAR(255) NOT NULL,
  `ticket_id` INT,
  `details` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_created_at` (`created_at`),
  INDEX `idx_action` (`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Event Settings Table
-- =============================================
CREATE TABLE IF NOT EXISTS `event_settings` (
  `id` INT PRIMARY KEY DEFAULT 1,
  `event_title` VARCHAR(255) DEFAULT 'Movie Night',
  `event_date` DATE,
  `event_time` TIME,
  `event_location` VARCHAR(255),
  `ticket_price` DECIMAL(10, 2) DEFAULT 15.00,
  `max_tickets` INT DEFAULT 50,
  `movie_poster_url` VARCHAR(500),
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============================================
-- Insert Default Event Settings
-- =============================================
INSERT INTO `event_settings` 
(`id`, `event_title`, `event_date`, `event_time`, `event_location`, `ticket_price`, `max_tickets`) 
VALUES 
(1, 'Movie Night', '2024-07-15', '19:00:00', 'City Cinema Hall', 15.00, 50);

-- =============================================
-- Create Views for Dashboard Statistics
-- =============================================

-- View: Ticket Statistics
CREATE OR REPLACE VIEW `ticket_stats` AS
SELECT 
  COUNT(*) AS total_tickets,
  SUM(CASE WHEN is_used = FALSE THEN 1 ELSE 0 END) AS remaining_tickets,
  SUM(CASE WHEN is_used = TRUE THEN 1 ELSE 0 END) AS checked_in,
  SUM(CASE WHEN is_used = FALSE THEN 1 ELSE 0 END) * 
    (SELECT ticket_price FROM event_settings LIMIT 1) AS revenue_remaining,
  SUM(CASE WHEN is_used = TRUE THEN 1 ELSE 0 END) * 
    (SELECT ticket_price FROM event_settings LIMIT 1) AS revenue_completed
FROM `tickets`;

-- =============================================
-- Sample Data (Optional - for testing)
-- =============================================
-- Uncomment to populate sample data

-- INSERT INTO `users` (`name`, `phone`, `email`) VALUES
-- ('John Doe', '+1234567890', 'john@example.com'),
-- ('Jane Smith', '+1234567891', 'jane@example.com'),
-- ('Bob Johnson', '+1234567892', 'bob@example.com');

-- =============================================
-- Indexes for Performance
-- =============================================
-- Already created above, but summary:
-- - users: phone (UNIQUE), email
-- - tickets: ticket_id (UNIQUE), qr_value (UNIQUE), is_used, created_at, user_id
-- - checkins: ticket_id, checked_in_at
-- - admin_logs: created_at, action
