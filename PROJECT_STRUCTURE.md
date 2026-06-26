# Movie Night QR Ticket System - Project Structure

```
movie-night-qr-tickets/
│
├── index.php                          # Home page
├── ticket.php                         # Digital ticket display
├── scanner.php                        # QR scanner page
├── status.php                         # Health check endpoint
│
├── admin/
│   ├── login.php                      # Admin login page
│   ├── dashboard.php                  # Admin dashboard
│   ├── ticket.php                     # Admin ticket view
│   ├── logout.php                     # Logout handler
│   └── api/
│       ├── login.php                  # Login API
│       └── operations.php             # Admin operations API
│
├── api/
│   ├── purchase.php                   # Purchase API endpoint
│   └── scan.php                       # Scanner API endpoint
│
├── config/
│   ├── db.php                         # Database configuration
│   ├── admin.php                      # Admin configuration
│   ├── functions.php                  # Helper functions
│   └── error-handler.php              # Error handling
│
├── database/
│   └── schema.sql                     # Database schema
│
├── assets/
│   ├── css/
│   │   └── style.css                  # Main stylesheet (dark theme)
│   └── js/
│       └── app.js                     # Main JavaScript
│
├── logs/                              # Log files (auto-created)
│
├── README.md                          # Project overview
├── INSTALLATION.md                    # Installation guide
├── .gitignore                         # Git ignore rules
└── PROJECT_STRUCTURE.md               # This file
```

## File Descriptions

### Frontend Pages
- **index.php** - Home page with event details and ticket purchase form
- **ticket.php** - Digital ticket display with QR code and download option
- **scanner.php** - QR scanner page using device camera
- **status.php** - Health check endpoint for monitoring

### Admin Pages
- **admin/login.php** - Secure admin login page
- **admin/dashboard.php** - Dashboard with statistics and ticket management
- **admin/ticket.php** - Detailed ticket view and management
- **admin/logout.php** - Session termination

### APIs
- **api/purchase.php** - Handles ticket purchase requests
- **api/scan.php** - Processes QR code scans and validation
- **admin/api/login.php** - Admin authentication
- **admin/api/operations.php** - Admin operations (toggle, delete, export)

### Configuration
- **config/db.php** - MySQL connection and constants
- **config/admin.php** - Admin settings and security functions
- **config/functions.php** - Reusable helper functions
- **config/error-handler.php** - Error and exception handling

### Database
- **database/schema.sql** - Complete MySQL schema with:
  - users table
  - tickets table
  - checkins table
  - admin_logs table
  - event_settings table
  - Views for statistics

### Assets
- **assets/css/style.css** - Dark theme with cinema aesthetic
  - Glassmorphism effects
  - Gold accents
  - Responsive design
  - Animations
- **assets/js/app.js** - Client-side functionality
  - Form handling
  - API requests
  - Scanner integration
  - Utilities

## Database Schema

### Tables
1. **users** - Attendee information
2. **tickets** - Ticket records with QR codes
3. **checkins** - Check-in history and timestamps
4. **admin_logs** - Audit trail for admin actions
5. **event_settings** - Event configuration

### Views
- **ticket_stats** - Aggregated ticket statistics

## Security Features

- ✅ SQL Injection prevention (prepared statements)
- ✅ XSS protection (HTML escaping)
- ✅ CSRF tokens
- ✅ Session management
- ✅ Password hashing (BCrypt)
- ✅ Admin authentication
- ✅ Input validation
- ✅ Security headers

## Key Features

### User Experience
- 🎫 One-click ticket purchase
- 📱 Digital QR tickets
- 📥 Download tickets as PNG
- 🎬 Event details display
- ✨ Smooth animations
- 📱 Fully responsive design

### Admin Features
- 🔐 Secure dashboard
- 📊 Real-time statistics
- 🔍 Search and filtering
- ✏️ Edit/delete tickets
- 📤 Export to CSV
- 📝 Audit logs

### Scanner Features
- 📷 Device camera integration
- ⚡ Real-time QR detection
- 🔔 Audio feedback
- 🎨 Visual animations
- ✓ Instant validation

## Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 8+
- **Database**: MySQL 5.7+
- **Libraries**:
  - Bootstrap 5 (optional UI framework)
  - html5-qrcode (QR scanning)
  - endroid/qr-code (QR generation)
  - html2canvas (ticket download)

## API Response Format

All APIs return JSON:
```json
{
  "success": true/false,
  "message": "Status message",
  "data": { /* Additional data */ }
}
```

## Environment Configuration

Key settings in `config/db.php`:
- `DB_HOST` - MySQL host
- `DB_USER` - MySQL user
- `DB_PASS` - MySQL password
- `DB_NAME` - Database name
- `MAX_TICKETS` - Maximum tickets for event
- `TICKET_PRICE` - Price per ticket

## Deployment Notes

1. Use HTTPS in production
2. Change admin credentials
3. Set proper file permissions (644 files, 755 directories)
4. Configure error logging
5. Set up database backups
6. Enable security headers
7. Use environment variables for sensitive data

## Performance Considerations

- Database indexes on frequently queried columns
- Optimized queries with LIMIT
- Caching for static content
- Gzip compression enabled
- CDN for static assets (recommended)

## Logging

- Error logs in `logs/error.log`
- Admin actions logged in `admin_logs` table
- Check-in history in `checkins` table

## Version

Current: **1.0.0**

Last Updated: June 2024
