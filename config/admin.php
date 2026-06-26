<?php
/**
 * Admin Configuration
 * Movie Night QR Ticket System
 */

// Admin credentials
// IMPORTANT: Change these immediately after setup!
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD_HASH', password_hash('admin123', PASSWORD_BCRYPT));

// Default admin credentials for comparison
$ADMIN_CREDENTIALS = [
    'username' => ADMIN_USERNAME,
    'password' => 'admin123' // Plain text for reference only
];

// Session configuration
define('ADMIN_SESSION_TIMEOUT', 3600); // 1 hour in seconds
define('ADMIN_SESSION_REFRESH', 600); // 10 minutes before expiry

// Allowed admin IP addresses (optional - leave empty to allow all)
// Format: ['192.168.1.1', '10.0.0.1']
$ALLOWED_IPS = [];

// Logger function for admin actions
function log_admin_action($conn, $action, $ticket_id = null, $details = null) {
    $stmt = $conn->prepare("
        INSERT INTO admin_logs (action, ticket_id, details, created_at)
        VALUES (?, ?, ?, NOW())
    ");
    $stmt->bind_param('sis', $action, $ticket_id, $details);
    return $stmt->execute();
}

// Security headers function
function set_security_headers() {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    header('Content-Security-Policy: default-src \'self\'; script-src \'self\' \'unsafe-inline\'; style-src \'self\' \'unsafe-inline\'');
}

// CSRF token generation
function generate_csrf_token() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

?>
