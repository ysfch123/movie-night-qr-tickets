<?php
/**
 * QR Scanner Page
 * Movie Night QR Ticket System
 */

require_once 'config/db.php';
require_once 'config/functions.php';

$event = get_event_details($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Scanner - Movie Night</title>
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
                <li><a href="#">Scanner</a></li>
                <li><a href="admin/login.php">Admin</a></li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="container">
            <div style="text-align: center; margin-bottom: 2rem;">
                <h1 style="color: var(--accent);">📱 QR Code Scanner</h1>
                <p style="color: var(--text-muted);">Scan tickets to verify entry</p>
            </div>

            <div class="scanner-container">
                <!-- Scanner -->
                <div id="qr-reader" style="width: 100%; border-radius: var(--border-radius); overflow: hidden; background: rgba(0,0,0,0.5); margin-bottom: 2rem;"></div>

                <!-- Result Display -->
                <div id="scan-result" style="display: none;"></div>

                <!-- Manual Input (Backup) -->
                <div class="card" style="margin-top: 2rem;">
                    <div class="card-header">
                        <h4>Manual Ticket Input</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="manual-qr">Enter QR Code Value (for testing)</label>
                            <input type="text" id="manual-qr" placeholder="Paste QR code value here">
                        </div>
                        <button onclick="Scanner.handleScan(document.getElementById('manual-qr').value)" class="btn btn-secondary">
                            Verify Ticket
                        </button>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script>
        // Initialize scanner on page load
        window.addEventListener('load', () => {
            Scanner.init();
        });
    </script>
</body>
</html>
