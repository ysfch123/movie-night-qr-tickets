<?php
/**
 * Scanner API Endpoint
 * Movie Night QR Ticket System
 */

header('Content-Type: application/json');

require_once '../config/db.php';
require_once '../config/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$qr_value = $data['qr_value'] ?? null;

if (!$qr_value) {
    echo json_encode(['success' => false, 'message' => 'QR value required']);
    exit;
}

// Get ticket details
$ticket = get_ticket_details($conn, $qr_value);

if (!$ticket) {
    echo json_encode([
        'success' => false,
        'used' => false,
        'message' => 'Invalid ticket'
    ]);
    exit;
}

if ($ticket['is_used']) {
    echo json_encode([
        'success' => false,
        'used' => true,
        'message' => 'Ticket already used',
        'ticket' => [
            'name' => $ticket['name'],
            'ticket_id' => $ticket['ticket_id'],
            'used_at' => $ticket['used_at']
        ]
    ]);
    exit;
}

// Mark ticket as used
$scanner_ip = $_SERVER['REMOTE_ADDR'] ?? null;
if (mark_ticket_used($conn, $ticket['id'], $scanner_ip)) {
    echo json_encode([
        'success' => true,
        'message' => 'Ticket validated',
        'ticket' => [
            'name' => $ticket['name'],
            'ticket_id' => $ticket['ticket_id'],
            'checked_in_at' => date('Y-m-d H:i:s')
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Failed to mark ticket as used'
    ]);
}

?>
