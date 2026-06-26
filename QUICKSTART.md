# Movie Night QR Ticket System - Quick Start

## 1️⃣ Database Setup (2 minutes)

```bash
# Create database
mysql -u root -p
CREATE DATABASE movie_night_db;

# Import schema
mysql -u root -p movie_night_db < database/schema.sql
```

## 2️⃣ Configuration (1 minute)

Edit `config/db.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'movie_night_db');
```

## 3️⃣ Upload Files (1 minute)

```bash
cp -r . /var/www/html/movie-night-qr-tickets/
chmod -R 755 /var/www/html/movie-night-qr-tickets/
```

## 4️⃣ Access Application (30 seconds)

- **User**: http://localhost/movie-night-qr-tickets/
- **Admin**: http://localhost/movie-night-qr-tickets/admin/
- **Default**: admin / admin123

## ✅ Done!

Your Movie Night QR Ticket System is ready to use!

---

## 📱 Testing the System

### As a User
1. Go to home page
2. Fill in name, phone, email
3. Click "Buy Ticket"
4. Download your digital ticket

### As Admin
1. Login with admin credentials
2. View dashboard statistics
3. Search for tickets
4. Mark tickets as used
5. Export attendee list

### As Venue Staff
1. Open scanner page
2. Allow camera access
3. Point camera at QR code
4. Get instant validation feedback

---

## 🔒 Important Security Steps

1. **Change admin password** immediately:
   - Edit `config/admin.php`
   - Generate new hash: `password_hash('newpassword', PASSWORD_BCRYPT)`

2. **Update database credentials**

3. **Enable HTTPS** in production

4. **Set file permissions**:
   ```bash
   chmod 644 config/*.php
   chmod 755 admin/ api/
   ```

---

## 📊 Features Included

✨ **User Features**
- Beautiful home page
- One-click ticket purchase
- Digital QR tickets
- Download/print support

🔐 **Admin Dashboard**
- Real-time statistics
- Ticket search & filter
- Mark used/unused
- Delete tickets
- Export CSV

📱 **QR Scanner**
- Camera integration
- Real-time validation
- Audio feedback
- Success/error animations

🎨 **Design**
- Dark theme
- Cinema aesthetic
- Glassmorphism effects
- Fully responsive
- Smooth animations

---

## 🆘 Troubleshooting

**Can't connect to database?**
- Check credentials in `config/db.php`
- Ensure MySQL is running
- Verify database exists

**Scanner not working?**
- Use HTTPS (required for camera)
- Check browser permissions
- Use modern browser

**Tickets not generating?**
- Check GD library is enabled
- Install QR library: `composer require endroid/qr-code`

---

## 📚 Documentation

- **Installation**: See `INSTALLATION.md`
- **Structure**: See `PROJECT_STRUCTURE.md`
- **README**: See `README.md`

---

**You're all set!** 🎉
