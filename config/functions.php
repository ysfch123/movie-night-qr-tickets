<?php
/**
 * Common Functions
 * Movie Night QR Ticket System
 */

// Sanitize input
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Validate email
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Validate phone number
function validate_phone($phone) {
    // Remove all non-digit characters
    $phone = preg_replace('/\D/', '', $phone);
    // Check if phone has at least 10 digits
    return strlen($phone) >= 10 && strlen($phone) <= 15;
}

// Generate unique ticket ID
function generate_ticket_id($conn) {
    do {
        $counter = rand(1, 9999);
        $ticket_id = 'MN-' . str_pad($counter, 4, '0', STR_PAD_LEFT);
        
        $stmt = $conn->prepare("SELECT id FROM tickets WHERE ticket_id = ?");
        $stmt->bind_param('s', $ticket_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    } while ($result->num_rows > 0);
    
    return $ticket_id;
}

// Generate QR code value
function generate_qr_value() {
    return bin2hex(random_bytes(16));
}

// Generate QR code image using external library
function generate_qr_code($value) {
    // Using endroid/qr-code or similar library
    // For now, return the value - will be replaced with actual QR generation
    return $value;
}

// Get event details
function get_event_details($conn) {
    $result = $conn->query("SELECT * FROM event_settings WHERE id = 1");
    return $result->fetch_assoc();
}

// Get ticket statistics
function get_ticket_stats($conn) {
    $result = $conn->query("SELECT * FROM ticket_stats");
    return $result->fetch_assoc();
}

// Check if event is sold out
function is_sold_out($conn) {
    $stats = get_ticket_stats($conn);
    $event = get_event_details($conn);
    return $stats['total_tickets'] >= $event['max_tickets'];
}

// Get remaining tickets
function get_remaining_tickets($conn) {
    $event = get_event_details($conn);
    $stats = get_ticket_stats($conn);
    return max(0, $event['max_tickets'] - $stats['total_tickets']);
}

// Format currency
function format_currency($amount) {
    return '$' . number_format($amount, 2);
}

// Format date
function format_date($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

// Format time
function format_time($time, $format = 'g:i A') {
    return date($format, strtotime($time));
}

// Create user and ticket
function create_ticket($conn, $name, $phone, $email = null) {
    $name = sanitize_input($name);
    $phone = sanitize_input($phone);
    $email = $email ? sanitize_input($email) : null;
    
    // Validate inputs
    if (empty($name) || strlen($name) < 2) {
        return ['success' => false, 'message' => 'Invalid name'];
    }
    
    if (!validate_phone($phone)) {
        return ['success' => false, 'message' => 'Invalid phone number'];
    }
    
    if ($email && !validate_email($email)) {
        return ['success' => false, 'message' => 'Invalid email address'];
    }
    
    // Check if sold out
    if (is_sold_out($conn)) {
        return ['success' => false, 'message' => 'Event is sold out'];
    }
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        // Check if phone already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE phone = ?");
        $stmt->bind_param('s', $phone);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            throw new Exception('This phone number already has a ticket');
        }
        $stmt->close();
        
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (name, phone, email) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $phone, $email);
        $stmt->execute();
        $user_id = $conn->insert_id;
        $stmt->close();
        
        // Generate ticket ID and QR code
        $ticket_id = generate_ticket_id($conn);
        $qr_value = generate_qr_value();
        
        // Insert ticket
        $stmt = $conn->prepare("
            INSERT INTO tickets (ticket_id, user_id, qr_code, qr_value, is_used)
            VALUES (?, ?, ?, ?, FALSE)
        ");
        $stmt->bind_param('siss', $ticket_id, $user_id, $qr_value, $qr_value);
        $stmt->execute();
        $ticket_db_id = $conn->insert_id;
        $stmt->close();
        
        $conn->commit();
        
        return [
            'success' => true,
            'message' => 'Ticket created successfully',
            'ticket_id' => $ticket_id,
            'qr_value' => $qr_value,
            'user_id' => $user_id,
            'db_id' => $ticket_db_id,
            'name' => $name
        ];
    } catch (Exception $e) {
        $conn->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Get ticket details
function get_ticket_details($conn, $qr_value) {
    $stmt = $conn->prepare("
        SELECT t.id, t.ticket_id, t.is_used, t.used_at, t.created_at, 
               u.name, u.phone, u.email
        FROM tickets t
        JOIN users u ON t.user_id = u.id
        WHERE t.qr_value = ?
    ");
    $stmt->bind_param('s', $qr_value);
    $stmt->execute();
    $result = $stmt->get_result();
    $ticket = $result->fetch_assoc();
    $stmt->close();
    
    return $ticket;
}

// Mark ticket as used
function mark_ticket_used($conn, $ticket_id, $scanner_ip = null) {
    $stmt = $conn->prepare("
        UPDATE tickets
        SET is_used = TRUE, used_at = NOW()
        WHERE id = ?
    ");
    $stmt->bind_param('i', $ticket_id);
    $result = $stmt->execute();
    $stmt->close();
    
    if ($result) {
        // Log check-in
        $stmt = $conn->prepare("
            INSERT INTO checkins (ticket_id, checked_in_at, scanner_ip)
            VALUES (?, NOW(), ?)
        ");
        $stmt->bind_param('is', $ticket_id, $scanner_ip);
        $stmt->execute();
        $stmt->close();
    }
    
    return $result;
}

// Mark ticket as unused
function mark_ticket_unused($conn, $ticket_id) {
    $stmt = $conn->prepare("
        UPDATE tickets
        SET is_used = FALSE, used_at = NULL
        WHERE id = ?
    ");
    $stmt->bind_param('i', $ticket_id);
    $result = $stmt->execute();
    $stmt->close();
    
    return $result;
}

// Get all tickets (for admin)
function get_all_tickets($conn, $search = null, $filter = 'all', $limit = 50, $offset = 0) {
    $query = "
        SELECT t.id, t.ticket_id, t.is_used, t.used_at, t.created_at,
               u.name, u.phone, u.email
        FROM tickets t
        JOIN users u ON t.user_id = u.id
        WHERE 1=1
    ";
    
    if ($search) {
        $search = '%' . sanitize_input($search) . '%';
        $query .= " AND (u.name LIKE ? OR u.phone LIKE ? OR t.ticket_id LIKE ?)";
    }
    
    if ($filter === 'used') {
        $query .= " AND t.is_used = TRUE";
    } elseif ($filter === 'unused') {
        $query .= " AND t.is_used = FALSE";
    }
    
    $query .= " ORDER BY t.created_at DESC LIMIT ? OFFSET ?";
    
    $stmt = $conn->prepare($query);
    
    if ($search) {
        $stmt->bind_param('sssii', $search, $search, $search, $limit, $offset);
    } else {
        $stmt->bind_param('ii', $limit, $offset);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $tickets = [];
    
    while ($row = $result->fetch_assoc()) {
        $tickets[] = $row;
    }
    
    $stmt->close();
    return $tickets;
}

// Get total ticket count (for pagination)
function get_total_ticket_count($conn, $search = null, $filter = 'all') {
    $query = "
        SELECT COUNT(*) as total
        FROM tickets t
        JOIN users u ON t.user_id = u.id
        WHERE 1=1
    ";
    
    if ($search) {
        $search = '%' . sanitize_input($search) . '%';
        $query .= " AND (u.name LIKE ? OR u.phone LIKE ? OR t.ticket_id LIKE ?)";
    }
    
    if ($filter === 'used') {
        $query .= " AND t.is_used = TRUE";
    } elseif ($filter === 'unused') {
        $query .= " AND t.is_used = FALSE";
    }
    
    $stmt = $conn->prepare($query);
    
    if ($search) {
        $stmt->bind_param('sss', $search, $search, $search);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    return $row['total'];
}

// Delete ticket
function delete_ticket($conn, $ticket_id) {
    $stmt = $conn->prepare("DELETE FROM tickets WHERE id = ?");
    $stmt->bind_param('i', $ticket_id);
    $result = $stmt->execute();
    $stmt->close();
    return $result;
}

// Export tickets to CSV
function export_tickets_csv($conn, $filter = 'all') {
    $query = "
        SELECT u.name, u.phone, u.email, t.ticket_id, t.is_used, t.created_at, t.used_at
        FROM tickets t
        JOIN users u ON t.user_id = u.id
        WHERE 1=1
    ";
    
    if ($filter === 'used') {
        $query .= " AND t.is_used = TRUE";
    } elseif ($filter === 'unused') {
        $query .= " AND t.is_used = FALSE";
    }
    
    $query .= " ORDER BY t.created_at DESC";
    
    $result = $conn->query($query);
    
    $csv = "Name,Phone,Email,Ticket ID,Status,Purchase Date,Used Date\n";
    
    while ($row = $result->fetch_assoc()) {
        $status = $row['is_used'] ? 'Used' : 'Unused';
        $used_date = $row['used_at'] ?: 'N/A';
        
        $csv .= '"' . $row['name'] . '","' . $row['phone'] . '","' . ($row['email'] ?: '') . '",';
        $csv .= '"' . $row['ticket_id'] . '","' . $status . '","' . $row['created_at'] . '","' . $used_date . "\"\n";
    }
    
    return $csv;
}

?>
