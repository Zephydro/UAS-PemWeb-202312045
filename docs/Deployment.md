# 🌍 Deployment Guide - HotelEase

## 📋 Daftar Isi

1. [Overview](#overview)
2. [Pre-Deployment Checklist](#pre-deployment-checklist)
3. [Shared Hosting Deployment](#shared-hosting-deployment)
4. [VPS/Cloud Server Deployment](#vpscloud-server-deployment)
5. [Domain & SSL Configuration](#domain--ssl-configuration)
6. [Production Configuration](#production-configuration)
7. [Database Migration](#database-migration)
8. [Performance Optimization](#performance-optimization)
9. [Security Hardening](#security-hardening)
10. [Monitoring & Maintenance](#monitoring--maintenance)

---

## 🎯 Overview

Guide ini menjelaskan cara men-deploy sistem HotelEase dari development environment (XAMPP) ke production server yang siap digunakan oleh publik.

### Deployment Options

| Method | Complexity | Cost | Performance | Recommended For |
|--------|------------|------|-------------|-----------------|
| **Shared Hosting** | ⭐ Easy | 💰 Low | ⚡ Basic | Small hotels, prototype |
| **VPS/Cloud** | ⭐⭐ Medium | 💰💰 Medium | ⚡⚡ Good | Growing business |
| **Dedicated Server** | ⭐⭐⭐ Hard | 💰💰💰 High | ⚡⚡⚡ Excellent | Large hotels |

---

## ✅ Pre-Deployment Checklist

### 🔍 Code Review
- [ ] **Remove Debug Code**: Hapus semua `echo`, `var_dump`, `print_r`
- [ ] **Error Reporting**: Set `display_errors = Off` untuk production
- [ ] **Database Credentials**: Gunakan credential production yang aman
- [ ] **File Paths**: Pastikan semua path relatif, bukan absolute
- [ ] **Comments**: Hapus comment debug dan temporary code

### 🗄️ Database Preparation
- [ ] **Export Database**: Backup lengkap database development
- [ ] **Sample Data**: Hapus data testing, sisakan data master
- [ ] **Admin Account**: Ganti password default admin
- [ ] **Indexing**: Pastikan semua index database optimal

### 📁 File Organization
- [ ] **Remove Dev Files**: Hapus file development seperti `phpinfo.php`
- [ ] **Optimize Images**: Compress gambar untuk web
- [ ] **CSS/JS Minification**: Minify file CSS dan JavaScript
- [ ] **Upload Folders**: Buat folder upload dengan permission yang benar

### 🔒 Security Check
- [ ] **Password Hashing**: Pastikan semua password di-hash dengan benar
- [ ] **SQL Injection**: Review semua query menggunakan prepared statements
- [ ] **XSS Protection**: Sanitize semua input dan output
- [ ] **File Upload**: Validasi tipe file dan ukuran upload

---

## 🌐 Shared Hosting Deployment

### Step 1: Choose Hosting Provider

**Recommended Specs:**
```
PHP: 7.4+ atau 8.0+
MySQL: 8.0+
Storage: Min 1GB
Bandwidth: Unlimited
SSL: Free SSL certificate
cPanel: Web-based control panel
```

**Popular Providers:**
- **Hostinger** - Budget-friendly, good performance
- **Niagahoster** - Local Indonesia, good support
- **SiteGround** - Premium features, excellent support
- **Bluehost** - WordPress optimized, reliable

### Step 2: Upload Files

#### Via cPanel File Manager
1. **Login ke cPanel**
2. **Open File Manager**
3. **Navigate to public_html**
4. **Upload ZIP file**
5. **Extract files**

#### Via FTP Client (Recommended)
```bash
# Using FileZilla or WinSCP
Host: ftp.yourdomain.com
Username: [from hosting provider]
Password: [from hosting provider]
Port: 21 (FTP) atau 22 (SFTP)

# Upload structure
public_html/
├── index.php
├── admin/
├── user/
├── auth/
├── config/
├── docs/
└── assets/
```

### Step 3: Database Setup

#### Create Database via cPanel
1. **MySQL Databases**
2. **Create New Database**: `yourdomain_hotelease`
3. **Create MySQL User**: `yourdomain_hotel`
4. **Set Password**: Generate strong password
5. **Add User to Database**: Grant ALL PRIVILEGES

#### Import Database
```bash
# Via phpMyAdmin
1. Login to phpMyAdmin
2. Select database
3. Click "Import"
4. Upload your .sql file
5. Execute import

# Via SSH (if available)
mysql -u yourdomain_hotel -p yourdomain_hotelease < backup.sql
```

### Step 4: Configuration Update

#### Update Database Config
```php
// config/koneksi.php
<?php
$host = 'localhost'; // or provided hostname
$user = 'yourdomain_hotel'; // your database user
$pass = 'your_secure_password'; // strong password
$db   = 'yourdomain_hotelease'; // your database name

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

#### Update Base URLs
```php
// config/config.php (create if needed)
<?php
define('BASE_URL', 'https://yourdomain.com/');
define('SITE_NAME', 'HotelEase');
define('ADMIN_EMAIL', 'admin@yourdomain.com');

// Production settings
ini_set('display_errors', 0);
error_reporting(0);
?>
```

### Step 5: File Permissions

```bash
# Set proper permissions
folders: 755
files: 644
uploads/: 777 (or 755 with proper ownership)

# Via cPanel or FTP
chmod 755 admin/
chmod 755 user/
chmod 644 *.php
chmod 777 uploads/
chmod 777 uploads/kamar/
chmod 777 uploads/bukti_bayar/
chmod 777 uploads/fasilitas/
```

---

## ☁️ VPS/Cloud Server Deployment

### Step 1: Server Setup

#### Choose VPS Provider
- **DigitalOcean** - Developer-friendly, SSD storage
- **Vultr** - Global locations, competitive pricing
- **Linode** - Excellent documentation, reliable
- **AWS EC2** - Scalable, enterprise-grade
- **Google Cloud** - AI integration, global network

#### Server Specifications
```
Minimum:
- 1 vCPU
- 1GB RAM
- 25GB SSD
- Ubuntu 20.04 LTS

Recommended:
- 2 vCPU
- 4GB RAM
- 50GB SSD
- Ubuntu 20.04 LTS
```

### Step 2: Server Initial Setup

#### Connect via SSH
```bash
ssh root@your_server_ip
```

#### Update System
```bash
apt update && apt upgrade -y
```

#### Install Required Software
```bash
# Install LAMP Stack
apt install apache2 -y
apt install mysql-server -y
apt install php libapache2-mod-php php-mysql -y

# Install PHP extensions
apt install php-mysqli php-gd php-curl php-mbstring php-zip php-xml -y

# Enable Apache modules
a2enmod rewrite
systemctl restart apache2
```

### Step 3: MySQL Configuration

#### Secure MySQL Installation
```bash
mysql_secure_installation
```

#### Create Database
```bash
mysql -u root -p

CREATE DATABASE hotelease CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
CREATE USER 'hotelease_user'@'localhost' IDENTIFIED BY 'secure_password123';
GRANT ALL PRIVILEGES ON hotelease.* TO 'hotelease_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### Step 4: Web Server Configuration

#### Apache Virtual Host
```bash
# Create site configuration
nano /etc/apache2/sites-available/hotelease.conf
```

```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    ServerAlias www.yourdomain.com
    DocumentRoot /var/www/hotelease
    
    <Directory /var/www/hotelease>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/hotelease_error.log
    CustomLog ${APACHE_LOG_DIR}/hotelease_access.log combined
</VirtualHost>
```

#### Enable Site
```bash
a2ensite hotelease.conf
a2dissite 000-default.conf
systemctl reload apache2
```

### Step 5: Deploy Application

#### Upload Files
```bash
# Create directory
mkdir -p /var/www/hotelease

# Upload via SCP
scp -r backup.putri/* root@your_server_ip:/var/www/hotelease/

# Or use Git
cd /var/www/hotelease
git clone [your-repository-url] .
```

#### Set Permissions
```bash
chown -R www-data:www-data /var/www/hotelease
chmod -R 755 /var/www/hotelease
chmod -R 777 /var/www/hotelease/uploads
```

#### Import Database
```bash
mysql -u hotelease_user -p hotelease < database_backup.sql
```

---

## 🔒 Domain & SSL Configuration

### Step 1: Domain Setup

#### Point Domain to Server
```
A Record: @ → your_server_ip
A Record: www → your_server_ip
CNAME: * → yourdomain.com (optional)
```

#### DNS Propagation Check
```bash
# Check DNS propagation
nslookup yourdomain.com
dig yourdomain.com
```

### Step 2: SSL Certificate (Let's Encrypt)

#### Install Certbot
```bash
apt install certbot python3-certbot-apache -y
```

#### Generate SSL Certificate
```bash
certbot --apache -d yourdomain.com -d www.yourdomain.com
```

#### Auto-renewal Setup
```bash
# Test auto-renewal
certbot renew --dry-run

# Add to crontab
crontab -e
0 12 * * * /usr/bin/certbot renew --quiet
```

#### Updated Virtual Host (Auto-generated)
```apache
<VirtualHost *:443>
    ServerName yourdomain.com
    DocumentRoot /var/www/hotelease
    
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/yourdomain.com/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/yourdomain.com/privkey.pem
    
    # Security headers
    Header always set Strict-Transport-Security "max-age=63072000; includeSubDomains; preload"
    Header always set X-Frame-Options DENY
    Header always set X-Content-Type-Options nosniff
</VirtualHost>
```

---

## ⚙️ Production Configuration

### PHP Configuration

#### Update php.ini
```bash
nano /etc/php/8.0/apache2/php.ini
```

```ini
# Error handling
display_errors = Off
log_errors = On
error_log = /var/log/php/error.log

# Performance
memory_limit = 256M
max_execution_time = 60
max_input_time = 60

# File uploads
upload_max_filesize = 10M
post_max_size = 10M
max_file_uploads = 20

# Security
expose_php = Off
allow_url_fopen = Off
allow_url_include = Off

# Session
session.cookie_httponly = 1
session.cookie_secure = 1
session.use_strict_mode = 1
```

### Environment Variables

#### Create .env file
```bash
nano /var/www/hotelease/.env
```

```env
# Database
DB_HOST=localhost
DB_NAME=hotelease
DB_USER=hotelease_user
DB_PASS=secure_password123

# Application
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Security
ENCRYPTION_KEY=your_32_character_secret_key
SESSION_LIFETIME=1440

# Email (if using)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_email@gmail.com
MAIL_PASSWORD=your_app_password
```

#### Update Config to Use Environment
```php
// config/koneksi.php
<?php
// Load environment variables
if (file_exists(__DIR__ . '/../.env')) {
    $env = parse_ini_file(__DIR__ . '/../.env');
    foreach ($env as $key => $value) {
        $_ENV[$key] = $value;
    }
}

$host = $_ENV['DB_HOST'] ?? 'localhost';
$user = $_ENV['DB_USER'] ?? 'root';
$pass = $_ENV['DB_PASS'] ?? '';
$db   = $_ENV['DB_NAME'] ?? 'hotelease';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    if ($_ENV['APP_DEBUG'] ?? false) {
        die("Connection failed: " . $conn->connect_error);
    } else {
        die("Database connection error. Please try again later.");
    }
}
?>
```

---

## 📊 Database Migration

### Production Database Setup

#### Optimize Database Schema
```sql
-- Add production indexes
CREATE INDEX idx_booking_created ON booking(created_at);
CREATE INDEX idx_booking_status_user ON booking(status, user_id);
CREATE INDEX idx_pembayaran_status ON pembayaran(status);
CREATE INDEX idx_users_role ON users(role);

-- Optimize tables
OPTIMIZE TABLE users, kamar, booking, pembayaran, fasilitas, staff, metode_pembayaran;

-- Update statistics
ANALYZE TABLE users, kamar, booking, pembayaran, fasilitas, staff, metode_pembayaran;
```

#### Production Data Cleanup
```sql
-- Remove test data
DELETE FROM booking WHERE nama LIKE '%test%' OR nama LIKE '%Test%';
DELETE FROM users WHERE username LIKE '%test%' AND role = 'customer';

-- Update admin credentials
UPDATE users SET 
    password = '$2y$10$NEW_SECURE_HASH_HERE',
    email = 'admin@yourdomain.com'
WHERE username = 'admin';
```

#### Database Backup Strategy
```bash
# Create backup script
nano /root/backup_db.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/mysql"
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u hotelease_user -p'secure_password123' hotelease > $BACKUP_DIR/hotelease_$DATE.sql

# Compress backup
gzip $BACKUP_DIR/hotelease_$DATE.sql

# Remove old backups (keep 7 days)
find $BACKUP_DIR -name "*.sql.gz" -mtime +7 -delete

echo "Backup completed: hotelease_$DATE.sql.gz"
```

```bash
# Make executable and schedule
chmod +x /root/backup_db.sh
crontab -e
0 2 * * * /root/backup_db.sh
```

---

## 🚀 Performance Optimization

### Web Server Optimization

#### Enable Compression
```apache
# Add to .htaccess
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
```

#### Browser Caching
```apache
# Add to .htaccess
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/pdf "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType application/x-javascript "access plus 1 month"
    ExpiresByType application/x-shockwave-flash "access plus 1 month"
    ExpiresByType image/x-icon "access plus 1 year"
    ExpiresDefault "access plus 2 days"
</IfModule>
```

#### Image Optimization
```bash
# Install image optimization tools
apt install jpegoptim optipng -y

# Optimize existing images
find uploads/ -name "*.jpg" -exec jpegoptim --max=85 {} \;
find uploads/ -name "*.png" -exec optipng -o2 {} \;
```

### Database Optimization

#### MySQL Configuration
```bash
nano /etc/mysql/mysql.conf.d/mysqld.cnf
```

```ini
[mysqld]
# Performance tuning
innodb_buffer_pool_size = 256M
innodb_log_file_size = 64M
innodb_flush_log_at_trx_commit = 2
innodb_flush_method = O_DIRECT

# Query cache
query_cache_type = 1
query_cache_size = 16M
query_cache_limit = 1M

# Connection settings
max_connections = 100
wait_timeout = 28800
interactive_timeout = 28800
```

#### Query Optimization
```php
// Implement query caching
class QueryCache {
    private static $cache = [];
    
    public static function get($key) {
        return self::$cache[$key] ?? null;
    }
    
    public static function set($key, $value, $ttl = 300) {
        self::$cache[$key] = [
            'data' => $value,
            'expires' => time() + $ttl
        ];
    }
    
    public static function isValid($key) {
        return isset(self::$cache[$key]) && 
               self::$cache[$key]['expires'] > time();
    }
}
```

### CDN Integration

#### CloudFlare Setup
1. **Sign up** for CloudFlare account
2. **Add your domain**
3. **Update nameservers**
4. **Configure settings**:
   - SSL: Full (strict)
   - Always Use HTTPS: On
   - Auto Minify: CSS, JS, HTML
   - Brotli compression: On

---

## 🔐 Security Hardening

### Server Security

#### Firewall Configuration
```bash
# Install UFW
apt install ufw -y

# Configure firewall
ufw default deny incoming
ufw default allow outgoing
ufw allow ssh
ufw allow 'Apache Full'
ufw enable
```

#### Fail2Ban Setup
```bash
# Install Fail2Ban
apt install fail2ban -y

# Configure Apache jail
nano /etc/fail2ban/jail.local
```

```ini
[apache-auth]
enabled = true
port = http,https
filter = apache-auth
logpath = /var/log/apache2/error.log
maxretry = 6
findtime = 600
bantime = 600

[apache-badbots]
enabled = true
port = http,https
filter = apache-badbots
logpath = /var/log/apache2/access.log
maxretry = 2
findtime = 600
bantime = 86400
```

#### Hide Server Information
```apache
# Add to Apache config
ServerTokens Prod
ServerSignature Off
```

### Application Security

#### Input Validation Class
```php
// security/InputValidator.php
class InputValidator {
    public static function sanitizeString($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
    
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }
    
    public static function validatePhone($phone) {
        return preg_match('/^[0-9+\-\s\(\)]+$/', $phone);
    }
    
    public static function validateFile($file, $allowedTypes = ['jpg', 'jpeg', 'png']) {
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        return in_array($ext, $allowedTypes) && $file['size'] <= 2048000;
    }
}
```

#### CSRF Protection
```php
// security/CSRFToken.php
class CSRFToken {
    public static function generate() {
        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        return $token;
    }
    
    public static function verify($token) {
        return isset($_SESSION['csrf_token']) && 
               hash_equals($_SESSION['csrf_token'], $token);
    }
}
```

#### Rate Limiting
```php
// security/RateLimit.php
class RateLimit {
    private static $attempts = [];
    
    public static function checkLimit($key, $maxAttempts = 5, $timeWindow = 300) {
        $now = time();
        $cleanupTime = $now - $timeWindow;
        
        // Cleanup old attempts
        if (isset(self::$attempts[$key])) {
            self::$attempts[$key] = array_filter(
                self::$attempts[$key], 
                function($time) use ($cleanupTime) {
                    return $time > $cleanupTime;
                }
            );
        }
        
        // Check current attempts
        $currentAttempts = count(self::$attempts[$key] ?? []);
        
        if ($currentAttempts >= $maxAttempts) {
            return false;
        }
        
        // Record attempt
        self::$attempts[$key][] = $now;
        return true;
    }
}
```

---

## 📊 Monitoring & Maintenance

### Monitoring Setup

#### Server Monitoring
```bash
# Install monitoring tools
apt install htop iotop nethogs -y

# Create monitoring script
nano /root/monitor.sh
```

```bash
#!/bin/bash
echo "=== Server Status $(date) ===" >> /var/log/server_status.log
echo "CPU Usage:" >> /var/log/server_status.log
top -bn1 | grep "Cpu(s)" >> /var/log/server_status.log
echo "Memory Usage:" >> /var/log/server_status.log
free -h >> /var/log/server_status.log
echo "Disk Usage:" >> /var/log/server_status.log
df -h >> /var/log/server_status.log
echo "=========================" >> /var/log/server_status.log
```

#### Application Monitoring
```php
// monitoring/SystemMonitor.php
class SystemMonitor {
    public static function logActivity($action, $user_id, $details = '') {
        global $conn;
        $stmt = $conn->prepare("
            INSERT INTO system_logs (action, user_id, details, ip_address, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $stmt->bind_param("siss", $action, $user_id, $details, $ip);
        $stmt->execute();
    }
    
    public static function checkDiskSpace() {
        $bytes = disk_free_space("/");
        $gbFree = $bytes / 1024 / 1024 / 1024;
        
        if ($gbFree < 1) {
            // Send alert
            self::sendAlert("Low disk space: " . round($gbFree, 2) . "GB remaining");
        }
        
        return $gbFree;
    }
    
    private static function sendAlert($message) {
        // Send email or notification
        error_log("ALERT: " . $message);
    }
}
```

### Backup Strategy

#### Automated Backup System
```bash
# Full backup script
nano /root/full_backup.sh
```

```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups"
APP_DIR="/var/www/hotelease"

mkdir -p $BACKUP_DIR/files
mkdir -p $BACKUP_DIR/database

# Backup files
tar -czf $BACKUP_DIR/files/hotelease_files_$DATE.tar.gz $APP_DIR

# Backup database
mysqldump -u hotelease_user -p'secure_password123' hotelease | gzip > $BACKUP_DIR/database/hotelease_db_$DATE.sql.gz

# Upload to remote storage (optional)
# aws s3 cp $BACKUP_DIR s3://your-backup-bucket/ --recursive

# Cleanup old backups (keep 30 days)
find $BACKUP_DIR -name "*.tar.gz" -mtime +30 -delete
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

echo "Full backup completed: $DATE"
```

### Update Strategy

#### Create Update Script
```bash
# nano /root/update_app.sh
#!/bin/bash
echo "Starting HotelEase update..."

# Backup current version
cp -r /var/www/hotelease /var/www/hotelease_backup_$(date +%Y%m%d)

# Pull updates (if using Git)
cd /var/www/hotelease
git pull origin main

# Update file permissions
chown -R www-data:www-data /var/www/hotelease
chmod -R 755 /var/www/hotelease
chmod -R 777 /var/www/hotelease/uploads

# Clear cache (if applicable)
# php artisan cache:clear

# Restart services
systemctl restart apache2

echo "Update completed!"
```

### Log Management

#### Centralized Logging
```php
// utils/Logger.php
class Logger {
    private static $logFile = '/var/log/hotelease.log';
    
    public static function info($message, $context = []) {
        self::writeLog('INFO', $message, $context);
    }
    
    public static function error($message, $context = []) {
        self::writeLog('ERROR', $message, $context);
    }
    
    public static function warning($message, $context = []) {
        self::writeLog('WARNING', $message, $context);
    }
    
    private static function writeLog($level, $message, $context) {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = empty($context) ? '' : ' | Context: ' . json_encode($context);
        $logEntry = "[$timestamp] [$level] $message$contextStr" . PHP_EOL;
        
        file_put_contents(self::$logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
}
```

#### Log Rotation
```bash
# Configure logrotate
nano /etc/logrotate.d/hotelease
```

```
/var/log/hotelease.log {
    daily
    missingok
    rotate 52
    compress
    delaycompress
    notifempty
    sharedscripts
    postrotate
        systemctl reload apache2
    endscript
}
```

---

## 🆘 Deployment Troubleshooting

### Common Issues

#### 1. 500 Internal Server Error
```bash
# Check error logs
tail -f /var/log/apache2/error.log
tail -f /var/log/hotelease.log

# Common fixes:
chmod -R 755 /var/www/hotelease
chown -R www-data:www-data /var/www/hotelease
```

#### 2. Database Connection Error
```bash
# Test database connection
mysql -u hotelease_user -p hotelease
SHOW TABLES;

# Check config file
cat /var/www/hotelease/config/koneksi.php
```

#### 3. SSL Certificate Issues
```bash
# Renew certificate
certbot renew --force-renewal

# Check certificate status
certbot certificates
```

#### 4. Performance Issues
```bash
# Check server resources
htop
df -h
free -h

# Check slow queries
mysql -u root -p
SHOW PROCESSLIST;
```

---

## 📚 Post-Deployment Checklist

### Final Verification
- [ ] **Homepage loads** correctly with HTTPS
- [ ] **Admin login** works with production credentials
- [ ] **User registration** and login functional
- [ ] **Booking system** working end-to-end
- [ ] **Payment upload** functioning properly
- [ ] **All images** displaying correctly
- [ ] **Mobile responsive** design working
- [ ] **SSL certificate** installed and valid
- [ ] **Backup system** running automatically
- [ ] **Monitoring** alerts configured

### Go-Live Tasks
- [ ] **DNS propagation** completed globally
- [ ] **Search engines** notified (Google Search Console)
- [ ] **Analytics** installed (Google Analytics)
- [ ] **Social media** links updated
- [ ] **Email** notifications configured
- [ ] **Support channels** established
- [ ] **User documentation** accessible
- [ ] **Staff training** completed

---

**🎉 Congratulations!** 

HotelEase is now successfully deployed to production. Your hotel management system is ready to serve customers worldwide!

---

## 📞 Support

Jika mengalami masalah deployment:

- 📧 **Email**: putrin151204@gmail.com
- 📖 **Documentation**: Baca troubleshooting section
- 🔧 **Server Issues**: Check error logs dan monitoring
- 💬 **Community**: PHP/MySQL community forums

---

*Made with ❤️ by Putri Nabila Az Zahra - HotelEase Deployment Guide*
