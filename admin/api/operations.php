<?php
/**
 * Admin Operations API
 * Movie Night QR Ticket System
 */

session_start();

header('Content-Type: application/json');

require_once '../../config/db.php';
require_once '../../config/admin.php';
require_once '../../config/functions.php';

// Check admin session
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$action = $data['action'] ?? null;

if (!$action) {
    echo json_encode(['success' => false, 'message' => 'Action required']);
    exit;
}

switch ($action) {
    case 'toggle_status':
        handle_toggle_status($conn, $data);
        break;
    
    case 'delete':
        handle_delete($conn, $data);
        break;
    
    case 'export_csv':
        handle_export_csv($conn, $data);
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
}

/**
 * Toggle ticket used status
 */
function handle_toggle_status($conn, $data) {
    $ticket_id = $data['ticket_id'] ?? null;
    $status = $data['status'] ?? null;
    
    if (!$ticket_id || $status === null) {
        echo json_encode(['success' => false, 'message' => 'Missing parameters']);
        return;
    }
    
    if ($status) {
        mark_ticket_used($conn, $ticket_id);
    } else {
        mark_ticket_unused($conn, $ticket_id);
    }
    
    log_admin_action($conn, $status ? 'mark_used' : 'mark_unused', $ticket_id);
    
    echo json_encode(['success' => true, 'message' => 'Status updated']);
}

/**
 * Delete ticket
 */
function handle_delete($conn, $data) {
    $ticket_id = $data['ticket_id'] ?? null;
    
    if (!$ticket_id) {
        echo json_encode(['success' => false, 'message' => 'Ticket ID required']);
        return;
    }
    
    delete_ticket($conn, $ticket_id);
    log_admin_action($conn, 'delete', $ticket_id);
    
    echo json_encode(['success' => true, 'message' => 'Ticket deleted']);
}

/**
 * Export tickets to CSV
 */
function handle_export_csv($conn, $data) {
    $filter = $data['filter'] ?? 'all';
    
    $csv = export_tickets_csv($conn, $filter);
    
    log_admin_action($conn, 'export_csv', null, $filter);
    
    echo json_encode([
        'success' => true,
        'csv' => $csv,
        'filename' => 'tickets_' . date('Y-m-d_His') . '.csv'
    ]);
}

?>
