<?php
/**
 * Error Handler
 * Movie Night QR Ticket System
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/logs/error.log');

// Create logs directory if it doesn't exist
if (!is_dir(dirname(__FILE__) . '/logs')) {
    mkdir(dirname(__FILE__) . '/logs', 0755, true);
}

// Custom error handler
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $error_message = "[$errno] $errstr in $errfile on line $errline";
    error_log($error_message);
    
    if (php_sapi_name() !== 'cli') {
        echo json_encode(['success' => false, 'message' => 'An error occurred']);
    }
});

// Exception handler
set_exception_handler(function($exception) {
    error_log('Exception: ' . $exception->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred']);
});

// Shutdown handler
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error !== null) {
        error_log('Fatal Error: ' . print_r($error, true));
    }
});

?>
