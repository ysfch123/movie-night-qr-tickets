<?php
/**
 * Health Check / Status Page
 * Movie Night QR Ticket System
 */

header('Content-Type: application/json');

require_once 'config/db.php';

$status = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'version' => '1.0.0',
    'database' => 'unknown'
];

// Check database
try {
    $result = $conn->query('SELECT 1');
    $status['database'] = 'connected';
    
    // Get stats
    $stats_result = $conn->query('SELECT * FROM ticket_stats');
    if ($stats_result) {
        $stats = $stats_result->fetch_assoc();
        $status['tickets'] = [
            'total' => $stats['total_tickets'] ?? 0,
            'checked_in' => $stats['checked_in'] ?? 0,
            'remaining' => $stats['remaining_tickets'] ?? 0
        ];
    }
} catch (Exception $e) {
    $status['status'] = 'error';
    $status['database'] = 'disconnected';
    $status['error'] = $e->getMessage();
}

echo json_encode($status);

?>
