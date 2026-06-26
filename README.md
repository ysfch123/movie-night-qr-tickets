# Movie Night QR Ticket System

A modern, mobile-friendly web application for managing movie night events with digital QR-code tickets.

## Features

✨ **User Features**
- Beautiful home page with event details
- One-click ticket purchase
- Digital QR ticket with download option
- Unique ticket IDs (MN-0001, MN-0002, etc.)

🔐 **Admin Dashboard**
- Secure login system
- Real-time ticket statistics
- Attendee management table
- Search and filter capabilities
- Mark tickets as used/unused

📱 **QR Scanner**
- Device camera integration
- Real-time QR code detection
- Success/error animations
- Audio feedback

🎨 **Design**
- Dark theme with cinema aesthetic
- Glassmorphism effects
- Gold accents
- Fully responsive design
- Smooth animations

## Tech Stack

- **Frontend**: HTML5, CSS3, JavaScript
- **Backend**: PHP 8+
- **Database**: MySQL
- **Libraries**: Bootstrap 5, html5-qrcode, QR code generation

## Installation

### Prerequisites
- PHP 8.0+
- MySQL 5.7+
- Web server (Apache/Nginx)

### Setup Steps

1. **Clone the repository**
```bash
git clone https://github.com/ysfch123/movie-night-qr-tickets.git
cd movie-night-qr-tickets
```

2. **Import the database**
```bash
mysql -u your_username -p your_database < database/schema.sql
```

3. **Configure database credentials**
Edit `config/db.php` with your database details:
```php
$db_host = 'localhost';
$db_user = 'your_user';
$db_pass = 'your_password';
$db_name = 'movie_night_db';
```

4. **Set admin credentials**
Edit `config/admin.php` and update the admin username/password

5. **Upload to your web server**
```bash
cp -r . /var/www/html/movie-night-qr-tickets/
```

6. **Access the application**
- User: `http://yourdomain.com/movie-night-qr-tickets/`
- Admin: `http://yourdomain.com/movie-night-qr-tickets/admin/`

## Project Structure

```
movie-night-qr-tickets/
├── index.php                 # Home page
├── purchase.php              # Ticket purchase
├── ticket.php                # Digital ticket display
├── scanner.php               # QR scanner
├── config/
│   ├── db.php               # Database connection
│   └── admin.php            # Admin credentials
├── admin/
│   ├── index.php            # Admin dashboard
│   ├── login.php            # Admin login
│   └── api.php              # Admin API endpoints
├── api/
│   ├── purchase.php         # Purchase endpoint
│   ├── scan.php             # Scanner endpoint
│   └── admin.php            # Admin operations
├── assets/
│   ├── css/
│   │   └── style.css        # Main stylesheet
│   ├── js/
│   │   └── app.js           # Main JavaScript
│   └── images/              # Images and icons
├── database/
│   └── schema.sql           # Database schema
└── README.md                # This file
```

## Default Credentials

**Admin Login:**
- Username: `admin`
- Password: `admin123`

⚠️ **IMPORTANT**: Change these credentials immediately after setup!

## Features Overview

### For Users
1. View event details on homepage
2. Purchase tickets with name, phone, and optional email
3. Receive unique QR ticket instantly
4. Download ticket as PNG
5. Show QR code at entrance

### For Administrators
1. Secure login to admin dashboard
2. View real-time statistics
3. Search tickets by name, phone, or ticket ID
4. Mark tickets as used/unused
5. Edit or delete tickets
6. Resend tickets to email
7. Export attendee list (optional)

### For Venue Staff
1. Access dedicated scanner page
2. Scan QR codes with device camera
3. Instant validation feedback
4. Audio and visual confirmations
5. Prevent duplicate check-ins

## Security Features

- SQL Injection prevention
- XSS protection
- CSRF tokens
- Input validation
- Secure password hashing
- Session management
- Admin route protection

## Database Schema

- **users**: Event attendees
- **tickets**: Ticket records with QR codes
- **checkins**: Check-in history and timestamps

## Customization

### Change Event Details
Edit the event details in `index.php`:
```php
$event = [
    'title' => 'Movie Night',
    'date' => '2024-07-15',
    'time' => '19:00',
    'location' => 'City Cinema',
    'price' => 15.00,
    'max_tickets' => 50
];
```

### Change Color Scheme
Edit `assets/css/style.css` to modify:
- Primary colors
- Gold accents
- Dark theme shades

## Troubleshooting

**Database connection error:**
- Verify MySQL is running
- Check credentials in `config/db.php`
- Ensure database is created

**QR scanner not working:**
- Allow camera permissions in browser
- Use HTTPS (required for camera access)
- Check browser compatibility

**Tickets not generating:**
- Verify database tables are created
- Check PHP error logs
- Ensure write permissions on assets folder

## License

This project is provided as-is for event management use.

## Support

For issues or questions, please check the documentation or contact support.

---

**Version**: 1.0.0  
**Last Updated**: June 2024
