<?php
/**
 * Digital Ticket Display Page
 * Movie Night QR Ticket System
 */

require_once 'config/db.php';
require_once 'config/functions.php';

// Check if ticket data exists in session or URL
$ticket_id = isset($_GET['id']) ? sanitize_input($_GET['id']) : null;
$ticket_data = null;

if ($ticket_id) {
    // Fetch ticket from database
    $stmt = $conn->prepare("SELECT t.id, t.ticket_id, t.qr_code, t.is_used, t.created_at, u.name FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.ticket_id = ?");
    $stmt->bind_param('s', $ticket_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ticket_data = $result->fetch_assoc();
    $stmt->close();
}

$event = get_event_details($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Digital Ticket - Movie Night</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="container">
            <div class="logo">🎬 Movie Night</div>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="scanner.php">Scanner</a></li>
                <li><a href="admin/login.php">Admin</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="container">
            <div style="text-align: center; margin-bottom: 2rem;">
                <h1 style="color: var(--accent);">🎫 Your Digital Ticket</h1>
                <p style="color: var(--text-muted);">Show this ticket at the entrance</p>
            </div>

            <?php if ($ticket_data): ?>
            <!-- Ticket Card -->
            <div class="ticket-card" id="ticket-container">
                <!-- Ticket Header -->
                <div class="ticket-header">
                    <div class="ticket-icon">🎭</div>
                    <div class="ticket-title"><?php echo htmlspecialchars($event['event_title']); ?></div>
                </div>

                <!-- Ticket Body -->
                <div class="ticket-body">
                    <div class="ticket-row">
                        <span class="ticket-label">TICKET HOLDER</span>
                        <span class="ticket-value" id="ticket-name"><?php echo htmlspecialchars($ticket_data['name']); ?></span>
                    </div>

                    <div class="ticket-row">
                        <span class="ticket-label">TICKET ID</span>
                        <span class="ticket-value" id="ticket-id"><?php echo htmlspecialchars($ticket_data['ticket_id']); ?></span>
                    </div>

                    <div class="ticket-row">
                        <span class="ticket-label">DATE</span>
                        <span class="ticket-value"><?php echo format_date($event['event_date']); ?></span>
                    </div>

                    <div class="ticket-row">
                        <span class="ticket-label">TIME</span>
                        <span class="ticket-value"><?php echo format_time($event['event_time']); ?></span>
                    </div>

                    <div class="ticket-row">
                        <span class="ticket-label">LOCATION</span>
                        <span class="ticket-value"><?php echo htmlspecialchars($event['event_location']); ?></span>
                    </div>

                    <?php if ($ticket_data['is_used']): ?>
                    <div class="ticket-row">
                        <span class="ticket-label">STATUS</span>
                        <span class="ticket-value" style="color: var(--error);">✓ USED</span>
                    </div>
                    <?php else: ?>
                    <div class="ticket-row">
                        <span class="ticket-label">STATUS</span>
                        <span class="ticket-value" style="color: var(--success);">✓ VALID</span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- QR Code -->
                <div class="qr-container">
                    <img id="ticket-qr" src="data:image/png;base64,<?php echo htmlspecialchars($ticket_data['qr_code']); ?>" alt="QR Code">
                    <p style="margin-top: 1rem; font-size: 0.75rem; color: var(--text-muted);">Scan this QR code at entrance</p>
                </div>

                <!-- Footer -->
                <div class="ticket-footer">
                    📱 Show this QR code at the entrance
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="text-align: center; margin-top: 2rem; display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <button onclick="Ticket.download()" class="btn btn-primary">
                    📥 Download as PNG
                </button>
                <button onclick="window.print()" class="btn btn-secondary">
                    🖨️ Print
                </button>
                <a href="index.php" class="btn btn-secondary">
                    🏠 Back Home
                </a>
            </div>

            <?php else: ?>
            <div class="alert alert-error" style="max-width: 500px; margin: 0 auto;">
                <h4>❌ Ticket Not Found</h4>
                <p>We couldn't find your ticket. Please purchase a new ticket.</p>
                <a href="index.php" class="btn btn-primary mt-3">Buy Ticket</a>
            </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2024 Movie Night QR Ticket System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="assets/js/app.js"></script>
</body>
</html>
