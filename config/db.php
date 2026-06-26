<?php
/**
 * Database Configuration
 * Movie Night QR Ticket System
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'movie_night_db');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die(json_encode([
        'success' => false,
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// Set charset to utf8
$conn->set_charset('utf8mb4');

// Define base URL
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/');

// Define API endpoints
define('API_BASE', BASE_URL . 'api/');

// Session timeout (in seconds)
define('SESSION_TIMEOUT', 3600); // 1 hour

// Max tickets for event
define('MAX_TICKETS', 50);

// Ticket price
define('TICKET_PRICE', 15.00);

// QR Code settings
define('QR_CODE_SIZE', 300);
define('QR_ERROR_CORRECTION', 'H'); // L, M, Q, H

// Security
define('JWT_SECRET', 'your-secret-key-change-this-in-production');
define('ADMIN_SESSION_NAME', 'admin_session');

?>
