<?php
/**
 * Admin Dashboard
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

// Set security headers
set_security_headers();

// Get search and filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : null;
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Get tickets
$tickets = get_all_tickets($conn, $search, $filter, $per_page, $offset);
$total = get_total_ticket_count($conn, $search, $filter);
$total_pages = ceil($total / $per_page);

// Get statistics
$stats = get_ticket_stats($conn);
$event = get_event_details($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Movie Night</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="container">
            <div class="logo">🎬 Movie Night Admin</div>
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="#" onclick="logout()">Logout</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="container">
            <h1 style="margin-bottom: 2rem;">📊 Admin Dashboard</h1>

            <!-- Statistics -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Tickets Sold</div>
                    <div class="stat-value"><?php echo $stats['total_tickets'] ?? 0; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Remaining Tickets</div>
                    <div class="stat-value"><?php echo $stats['remaining_tickets'] ?? 0; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Checked In</div>
                    <div class="stat-value"><?php echo $stats['checked_in'] ?? 0; ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Revenue</div>
                    <div class="stat-value">$<?php echo number_format(($stats['checked_in'] ?? 0) * ($event['ticket_price'] ?? 0), 2); ?></div>
                </div>
            </div>

            <!-- Search and Filter -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3>🔍 Search & Filter</h3>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-6">
                            <input type="text" name="search" class="form-control" placeholder="Search by name, phone, or ticket ID" value="<?php echo htmlspecialchars($search ?? ''); ?>">
                        </div>
                        <div class="col-md-3">
                            <select name="filter" class="form-select">
                                <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Tickets</option>
                                <option value="used" <?php echo $filter === 'used' ? 'selected' : ''; ?>>Used</option>
                                <option value="unused" <?php echo $filter === 'unused' ? 'selected' : ''; ?>>Unused</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tickets Table -->
            <div class="card">
                <div class="card-header">
                    <h3>🎫 Tickets</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Ticket ID</th>
                                    <th>Status</th>
                                    <th>Purchase Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (count($tickets) > 0): ?>
                                    <?php foreach ($tickets as $ticket): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($ticket['name']); ?></td>
                                        <td><?php echo htmlspecialchars($ticket['phone']); ?></td>
                                        <td><strong><?php echo htmlspecialchars($ticket['ticket_id']); ?></strong></td>
                                        <td>
                                            <?php if ($ticket['is_used']): ?>
                                                <span class="badge badge-success">✓ Used</span>
                                            <?php else: ?>
                                                <span class="badge badge-error">Unused</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo format_date($ticket['created_at']); ?></td>
                                        <td>
                                            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                                <a href="ticket.php?id=<?php echo $ticket['id']; ?>" class="btn btn-small btn-secondary">View</a>
                                                <button onclick="toggleStatus(<?php echo $ticket['id']; ?>, <?php echo $ticket['is_used'] ? 0 : 1; ?>)" class="btn btn-small btn-secondary">
                                                    <?php echo $ticket['is_used'] ? 'Mark Unused' : 'Mark Used'; ?>
                                                </button>
                                                <button onclick="deleteTicket(<?php echo $ticket['id']; ?>)" class="btn btn-small btn-error">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 2rem;">
                                            No tickets found
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                    <div style="margin-top: 2rem; display: flex; justify-content: center; gap: 0.5rem;">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search ?? ''); ?>&filter=<?php echo $filter; ?>" class="btn btn-small <?php echo $page === $i ? 'btn-primary' : 'btn-secondary'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; 2024 Movie Night QR Ticket System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/app.js"></script>
    <script>
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        }

        function toggleStatus(ticketId, newStatus) {
            Utils.apiRequest('api/admin.php', 'POST', {
                action: 'toggle_status',
                ticket_id: ticketId,
                status: newStatus
            }).then(result => {
                if (result.success) {
                    Utils.notify('Status updated', 'success', 2000);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    Utils.notify(result.message || 'Failed to update status', 'error');
                }
            });
        }

        function deleteTicket(ticketId) {
            if (confirm('Are you sure you want to delete this ticket?')) {
                Utils.apiRequest('api/admin.php', 'POST', {
                    action: 'delete',
                    ticket_id: ticketId
                }).then(result => {
                    if (result.success) {
                        Utils.notify('Ticket deleted', 'success', 2000);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Utils.notify(result.message || 'Failed to delete ticket', 'error');
                    }
                });
            }
        }
    </script>
</body>
</html>
