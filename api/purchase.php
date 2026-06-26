<?php
/**
 * Purchase API Endpoint
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

if (!$data) {
    $data = $_POST;
}

$name = $data['name'] ?? null;
$phone = $data['phone'] ?? null;
$email = $data['email'] ?? null;

// Create ticket
$result = create_ticket($conn, $name, $phone, $email);

if ($result['success']) {
    // Generate QR code using a simple library
    // For production, use composer require endroid/qr-code
    $qr_code = base64_encode(generateQRCode($result['qr_value']));
    
    // Update QR code in database
    $stmt = $conn->prepare("UPDATE tickets SET qr_code = ? WHERE id = ?");
    $stmt->bind_param('si', $qr_code, $result['db_id']);
    $stmt->execute();
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Ticket created successfully',
        'ticket_id' => $result['ticket_id'],
        'qr_value' => $result['qr_value'],
        'qr_code' => $qr_code,
        'name' => $result['name']
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $result['message']
    ]);
}

/**
 * Generate QR Code (using library or placeholder)
 */
function generateQRCode($value) {
    // Placeholder QR code generation
    // In production, install: composer require endroid/qr-code
    // Then use: new QrCode($value);
    
    // For now, return a simple placeholder
    return createPlaceholderQR($value);
}

function createPlaceholderQR($value) {
    // Create a simple PNG image as placeholder
    $img = imagecreate(200, 200);
    $bg = imagecolorallocate($img, 255, 255, 255);
    $text_color = imagecolorallocate($img, 0, 0, 0);
    imagestring($img, 5, 10, 90, $value, $text_color);
    
    ob_start();
    imagepng($img);
    $image_data = ob_get_clean();
    imagedestroy($img);
    
    return $image_data;
}

?>
