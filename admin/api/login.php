<?php
/**
 * Admin Login API
 * Movie Night QR Ticket System
 */

session_start();

header('Content-Type: application/json');

require_once '../config/admin.php';
require_once '../config/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    $data = $_POST;
}

$username = $data['username'] ?? null;
$password = $data['password'] ?? null;

if (!$username || !$password) {
    echo json_encode(['success' => false, 'message' => 'Username and password required']);
    exit;
}

// Verify credentials
if ($username === ADMIN_USERNAME && password_verify($password, ADMIN_PASSWORD_HASH)) {
    $_SESSION['admin_logged_in'] = true;
    $_SESSION['admin_username'] = $username;
    $_SESSION['admin_login_time'] = time();
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'redirect' => 'dashboard.php'
    ]);
} else {
    // Log failed attempt (optional)
    error_log("Failed admin login attempt from {$_SERVER['REMOTE_ADDR']} for user: $username");
    
    echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
}

?>
