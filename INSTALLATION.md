# Installation & Setup Guide

## Prerequisites
- PHP 8.0 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Composer (optional, for QR code library)

## Step 1: Database Setup

### Create Database
```bash
mysql -u root -p
CREATE DATABASE movie_night_db;
```

### Import Schema
```bash
mysql -u root -p movie_night_db < database/schema.sql
```

## Step 2: Configure Database Connection

Edit `config/db.php` and update these lines:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'movie_night_db');
```

## Step 3: Configure Admin Credentials

Edit `config/admin.php` to change default credentials:
```php
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD_HASH', password_hash('admin123', PASSWORD_BCRYPT));
```

## Step 4: Install QR Code Library (Optional but Recommended)

```bash
composer require endroid/qr-code
```

Then update `api/purchase.php` to use the proper library instead of the placeholder.

## Step 5: Upload to Web Server

```bash
cp -r . /var/www/html/movie-night-qr-tickets/
chmod -R 755 /var/www/html/movie-night-qr-tickets/
```

## Step 6: Access the Application

- **User Home**: `http://localhost/movie-night-qr-tickets/`
- **Scanner**: `http://localhost/movie-night-qr-tickets/scanner.php`
- **Admin**: `http://localhost/movie-night-qr-tickets/admin/login.php`

## Default Credentials

**Admin Login:**
- Username: `admin`
- Password: `admin123`

⚠️ **CHANGE THESE IMMEDIATELY AFTER SETUP!**

## Configuration

### Event Details

Edit the event details in `database/schema.sql` or update via:
```php
UPDATE event_settings SET
  event_title = 'Your Event Title',
  event_date = '2024-07-15',
  event_time = '19:00:00',
  event_location = 'Your Location',
  ticket_price = 15.00,
  max_tickets = 50;
```

### Customization

- **Colors**: Edit `assets/css/style.css` - change CSS variables
- **Logo**: Replace emoji in HTML files with your logo
- **Max Tickets**: Update `MAX_TICKETS` in `config/db.php`
- **Ticket Price**: Update `TICKET_PRICE` in `config/db.php`

## Features

### For Users
- View event details
- Purchase tickets with QR code
- Download ticket as PNG
- Share digital ticket

### For Admin
- Secure dashboard
- Real-time statistics
- Search and filter tickets
- Mark tickets as used/unused
- Delete tickets
- Export to CSV

### For Venue Staff
- QR code scanner with camera
- Real-time validation
- Success/error feedback
- Audio alerts

## Troubleshooting

### Database Connection Error
```
Database connection failed: ...
```
**Solution**: Check credentials in `config/db.php`

### QR Scanner Not Working
```
Failed to start camera
```
**Solutions**:
- Use HTTPS (required for camera access)
- Check browser permissions
- Use modern browser (Chrome, Firefox, Safari)

### Tickets Not Generating
```
Could not generate QR code
```
**Solutions**:
- Install composer library: `composer require endroid/qr-code`
- Check GD library is enabled in PHP

### Session/Login Issues
```
Session expired
```
**Solutions**:
- Check PHP session settings
- Verify session storage directory has write permissions
- Increase session timeout in `config/db.php`

## Security Notes

1. **Change admin credentials** immediately
2. **Use HTTPS** in production
3. **Update database credentials** in production
4. **Regular backups** of database
5. **Secure file permissions** (644 for files, 755 for directories)
6. **Disable file listing**: Add to `.htaccess`
```
Options -Indexes
```

## Performance Tips

1. Enable database indexes (already included)
2. Cache event settings
3. Use CDN for static assets
4. Optimize images
5. Enable gzip compression

## API Endpoints

### Purchase Ticket
```
POST /api/purchase.php
{
  "name": "John Doe",
  "phone": "+1234567890",
  "email": "john@example.com"
}
```

### Scan Ticket
```
POST /api/scan.php
{
  "qr_value": "<qr_value>"
}
```

### Admin Login
```
POST /admin/api/login.php
{
  "username": "admin",
  "password": "admin123"
}
```

### Admin Operations
```
POST /admin/api/operations.php
{
  "action": "toggle_status|delete|export_csv",
  "ticket_id": 1,
  "status": 1,
  "filter": "all|used|unused"
}
```

## Support

For issues or questions, check:
1. Database logs
2. PHP error logs
3. Browser console errors
4. Check file permissions

## License

This project is provided as-is for event management use.
