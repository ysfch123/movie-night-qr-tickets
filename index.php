<?php
/**
 * Home Page - Movie Night QR Ticket System
 */

require_once 'config/db.php';
require_once 'config/functions.php';

// Get event details
$event = get_event_details($conn);
$stats = get_ticket_stats($conn);
$remaining = get_remaining_tickets($conn);
$is_sold_out = is_sold_out($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Night QR Tickets - Digital Ticket System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .sold-out-banner {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            padding: 1rem;
            text-align: center;
            border-radius: 12px;
            margin-bottom: 2rem;
            animation: pulse 2s ease-in-out infinite;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="container">
            <div class="logo">
                🎬 Movie Night
            </div>
            <ul>
                <li><a href="#">Home</a></li>
                <li><a href="scanner.php">Scanner</a></li>
                <li><a href="admin/login.php">Admin</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Hero Section -->
            <div class="hero">
                <h1>🎭 Movie Night</h1>
                <p class="subtitle">Get Your Digital Ticket Today</p>
            </div>

            <!-- Sold Out Banner -->
            <?php if ($is_sold_out): ?>
            <div class="sold-out-banner">
                <h3>🎟️ SOLD OUT</h3>
                <p>All tickets for this event have been sold. Thank you!</p>
            </div>
            <?php endif; ?>

            <div class="row">
                <!-- Movie Poster and Event Details -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="movie-poster">
                                <div style="width: 100%; height: 400px; background: linear-gradient(135deg, rgba(212, 175, 55, 0.2), rgba(212, 175, 55, 0.1)); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 3rem;">
                                    🎥
                                </div>
                            </div>
                            <p style="color: var(--text-muted); font-style: italic; margin-top: 1rem;">Movie Poster</p>
                        </div>
                    </div>
                </div>

                <!-- Ticket Purchase Form -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h3>📋 Get Your Ticket</h3>
                        </div>
                        <div class="card-body">
                            <!-- Event Details -->
                            <div class="event-details mb-4">
                                <div class="detail-item">
                                    <div class="label">📅 Date</div>
                                    <div class="value"><?php echo format_date($event['event_date']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="label">🕐 Time</div>
                                    <div class="value"><?php echo format_time($event['event_time']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="label">📍 Location</div>
                                    <div class="value"><?php echo htmlspecialchars($event['event_location']); ?></div>
                                </div>
                                <div class="detail-item">
                                    <div class="label">💰 Price</div>
                                    <div class="value"><?php echo format_currency($event['ticket_price']); ?></div>
                                </div>
                            </div>

                            <!-- Ticket Count -->
                            <div class="alert alert-info mb-4">
                                <strong>🎟️ Remaining Tickets:</strong> <?php echo $remaining; ?> / <?php echo $event['max_tickets']; ?>
                            </div>

                            <!-- Purchase Form -->
                            <?php if (!$is_sold_out): ?>
                            <form id="purchase-form">
                                <div class="form-group">
                                    <label for="name">Full Name *</label>
                                    <input type="text" id="name" name="name" required placeholder="John Doe" minlength="2">
                                </div>

                                <div class="form-group">
                                    <label for="phone">Phone Number *</label>
                                    <input type="tel" id="phone" name="phone" required placeholder="+1 (555) 123-4567">
                                </div>

                                <div class="form-group">
                                    <label for="email">Email (Optional)</label>
                                    <input type="email" id="email" name="email" placeholder="your@email.com">
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    🎫 Buy Ticket - <?php echo format_currency($event['ticket_price']); ?>
                                </button>
                            </form>
                            <?php else: ?>
                            <div class="alert alert-error">
                                <strong>Event is sold out</strong>
                            </div>
                            <button class="btn btn-secondary w-100" disabled>
                                Tickets Sold Out
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
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
    <script src="assets/js/app.js"></script>
</body>
</html>
