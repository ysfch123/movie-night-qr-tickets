<?php
/**
 * Admin Login Page
 * Movie Night QR Ticket System
 */

require_once '../config/db.php';
require_once '../config/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Movie Night</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="container">
            <div class="logo">🎬 Movie Night Admin</div>
            <ul>
                <li><a href="../index.php">Home</a></li>
            </ul>
        </div>
    </nav>

    <!-- Login Form -->
    <main>
        <div class="container">
            <div style="max-width: 400px; margin: 4rem auto;">
                <div class="card">
                    <div class="card-header">
                        <h2 style="text-align: center; margin: 0;">🔐 Admin Login</h2>
                    </div>
                    <div class="card-body">
                        <form id="admin-login-form">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" id="username" name="username" required autofocus>
                            </div>

                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" id="password" name="password" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                Login
                            </button>
                        </form>
                    </div>
                </div>

                <div class="alert alert-info mt-3" style="font-size: 0.875rem;">
                    <strong>Demo Credentials:</strong><br>
                    Username: <code>admin</code><br>
                    Password: <code>admin123</code><br>
                    <em>⚠️ Change these immediately after login!</em>
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
</body>
</html>
