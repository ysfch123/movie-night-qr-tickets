<?php
/**
 * View Ticket (Admin)
 * Movie Night QR Ticket System
 */

session_start();

require_once '../config/db.php';
require_once '../config/admin.php';
require_once '../config/functions.php';

// Check admin session
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$ticket_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$ticket_id) {
    header('Location: dashboard.php');
    exit;
}

// Get ticket
$stmt = $conn->prepare("SELECT t.*, u.name, u.phone, u.email FROM tickets t JOIN users u ON t.user_id = u.id WHERE t.id = ?");
$stmt->bind_param('i', $ticket_id);
$stmt->execute();
$result = $stmt->get_result();
$ticket = $result->fetch_assoc();
$stmt->close();

if (!$ticket) {
    header('Location: dashboard.php');
    exit;
}

$event = get_event_details($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Ticket - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <nav>
        <div class="container">
            <div class="logo">🎬 Movie Night Admin</div>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="#" onclick="logout()">Logout</a></li>
            </ul>
        </div>
    </nav>

    <main>
        <div class="container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
                <h1>🎫 Ticket Details</h1>
                <a href="dashboard.php" class="btn btn-secondary">Back</a>
            </div>

            <div class="row">
                <div class="col-md-8 mb-4">
                    <div class="ticket-card">
                        <div class="ticket-header">
                            <div class="ticket-icon">🎭</div>
                            <div class="ticket-title"><?php echo htmlspecialchars($event['event_title']); ?></div>
                        </div>

                        <div class="ticket-body">
                            <div class="ticket-row">
                                <span class="ticket-label">TICKET HOLDER</span>
                                <span class="ticket-value"><?php echo htmlspecialchars($ticket['name']); ?></span>
                            </div>
                            <div class="ticket-row">
                                <span class="ticket-label">PHONE</span>
                                <span class="ticket-value"><?php echo htmlspecialchars($ticket['phone']); ?></span>
                            </div>
                            <div class="ticket-row">
                                <span class="ticket-label">EMAIL</span>
                                <span class="ticket-value"><?php echo htmlspecialchars($ticket['email'] ?: 'N/A'); ?></span>
                            </div>
                            <div class="ticket-row">
                                <span class="ticket-label">TICKET ID</span>
                                <span class="ticket-value"><?php echo htmlspecialchars($ticket['ticket_id']); ?></span>
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
                            <div class="ticket-row">
                                <span class="ticket-label">STATUS</span>
                                <span class="ticket-value" style="color: <?php echo $ticket['is_used'] ? 'var(--error)' : 'var(--success)'; ?>;">
                                    <?php echo $ticket['is_used'] ? '✓ USED' : '✓ VALID'; ?>
                                </span>
                            </div>
                            <?php if ($ticket['is_used']): ?>
                            <div class="ticket-row">
                                <span class="ticket-label">USED AT</span>
                                <span class="ticket-value"><?php echo format_date($ticket['used_at']); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="ticket-row">
                                <span class="ticket-label">PURCHASED</span>
                                <span class="ticket-value"><?php echo format_date($ticket['created_at']); ?></span>
                            </div>
                        </div>

                        <div class="qr-container">
                            <img src="data:image/png;base64,<?php echo htmlspecialchars($ticket['qr_code']); ?>" alt="QR Code">
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>📝 Actions</h4>
                        </div>
                        <div class="card-body">
                            <button onclick="toggleStatus(<?php echo $ticket['id']; ?>, <?php echo $ticket['is_used'] ? 0 : 1; ?>)" class="btn btn-primary w-100 mb-2">
                                <?php echo $ticket['is_used'] ? 'Mark as Unused' : 'Mark as Used'; ?>
                            </button>
                            <button onclick="downloadTicket()" class="btn btn-secondary w-100 mb-2">
                                Download
                            </button>
                            <button onclick="printTicket()" class="btn btn-secondary w-100 mb-2">
                                Print
                            </button>
                            <button onclick="deleteTicket(<?php echo $ticket['id']; ?>)" class="btn btn-error w-100">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2024 Movie Night QR Ticket System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
    <script>
        function toggleStatus(ticketId, newStatus) {
            Utils.apiRequest('api/operations.php', 'POST', {
                action: 'toggle_status',
                ticket_id: ticketId,
                status: newStatus
            }).then(result => {
                if (result.success) {
                    Utils.notify('Status updated', 'success');
                    setTimeout(() => location.reload(), 1500);
                }
            });
        }

        function deleteTicket(ticketId) {
            if (confirm('Are you sure?')) {
                Utils.apiRequest('api/operations.php', 'POST', {
                    action: 'delete',
                    ticket_id: ticketId
                }).then(result => {
                    if (result.success) {
                        Utils.notify('Ticket deleted', 'success');
                        setTimeout(() => location.href = 'dashboard.php', 1500);
                    }
                });
            }
        }

        function downloadTicket() {
            const ticketCard = document.querySelector('.ticket-card');
            if (typeof html2canvas !== 'undefined') {
                html2canvas(ticketCard).then(canvas => {
                    const link = document.createElement('a');
                    link.href = canvas.toDataURL();
                    link.download = 'ticket.png';
                    link.click();
                });
            }
        }

        function printTicket() {
            window.print();
        }

        function logout() {
            if (confirm('Logout?')) {
                window.location.href = 'logout.php';
            }
        }
    </script>
</body>
</html>
