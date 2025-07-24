# 🚀 Installation Guide - HotelEase

## 📋 Daftar Isi

1. [Prerequisites](#prerequisites)
2. [Download & Setup](#download--setup)
3. [Database Configuration](#database-configuration)
4. [System Configuration](#system-configuration)
5. [First Run](#first-run)
6. [Troubleshooting](#troubleshooting)
7. [Verification](#verification)

---

## 🛠️ Prerequisites

Sebelum menginstall HotelEase, pastikan sistem Anda memenuhi requirement berikut:

### Server Requirements

| Requirement | Version | Description |
|-------------|---------|-------------|
| **PHP** | 7.4+ | Server-side scripting |
| **MySQL** | 8.0+ | Database management |
| **Apache/Nginx** | Latest | Web server |
| **Web Browser** | Modern | Chrome, Firefox, Safari, Edge |

### XAMPP/WAMP/LAMP Stack
- **XAMPP** (Windows/Mac/Linux) - **Recommended**
- **WAMP** (Windows)
- **LAMP** (Linux)
- **MAMP** (Mac)

### PHP Extensions
Pastikan extension berikut enabled:
```ini
extension=mysqli
extension=pdo_mysql
extension=gd
extension=mbstring
extension=curl
extension=zip
```

---

## 📥 Download & Setup

### Method 1: Direct Download

1. **Download Project**
   ```bash
   # Download ZIP file dari repository
   # Extract ke folder htdocs XAMPP
   ```

2. **Copy ke Directory**
   ```bash
   # Windows XAMPP
   C:\xampp\htdocs\backup.putri\
   
   # Linux XAMPP
   /opt/lampp/htdocs/backup.putri/
   
   # Mac XAMPP
   /Applications/XAMPP/xamppfiles/htdocs/backup.putri/
   ```

### Method 2: Git Clone

```bash
# Clone repository
git clone [repository-url] backup.putri

# Pindah ke directory XAMPP
mv backup.putri /path/to/xampp/htdocs/

# Atau copy
cp -r backup.putri /path/to/xampp/htdocs/
```

---

## 🗄️ Database Configuration

### Step 1: Start XAMPP Services

```bash
# Windows - XAMPP Control Panel
# Start Apache dan MySQL

# Linux
sudo /opt/lampp/lampp start

# Or individual services
sudo /opt/lampp/lampp startapache
sudo /opt/lampp/lampp startmysql
```

### Step 2: Access phpMyAdmin

```
http://localhost/phpmyadmin
```

### Step 3: Create Database

#### Manual Database Creation

1. **Create New Database**
   ```sql
   CREATE DATABASE hotelease 
   CHARACTER SET utf8mb4 
   COLLATE utf8mb4_general_ci;
   ```

2. **Import Database Schema**
   - Klik database `hotelease`
   - Pilih tab "Import"
   - Upload file `database/hotelease.sql` (jika tersedia)
   - Klik "Go"

#### Using SQL Commands

```sql
-- 1. Create Database
CREATE DATABASE hotelease CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE hotelease;

-- 2. Create Tables
-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Kamar Table
CREATE TABLE kamar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipe_kamar VARCHAR(100) NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    fasilitas TEXT,
    tersedia BOOLEAN DEFAULT TRUE,
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Booking Table
CREATE TABLE booking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    nama VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    tipe_kamar VARCHAR(100) NOT NULL,
    jumlah_tamu INT NOT NULL,
    jumlah_malam INT NOT NULL,
    harga_total DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'diterima', 'ditolak', 'selesai') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Metode Pembayaran Table
CREATE TABLE metode_pembayaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_metode VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    nomor_rekening VARCHAR(50),
    atas_nama VARCHAR(100),
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Pembayaran Table
CREATE TABLE pembayaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    metode_pembayaran_id INT,
    jumlah DECIMAL(10,2) NOT NULL,
    status ENUM('pending', 'dibayar', 'gagal') DEFAULT 'pending',
    bukti_bayar VARCHAR(255),
    tanggal_pembayaran TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES booking(id) ON DELETE CASCADE,
    FOREIGN KEY (metode_pembayaran_id) REFERENCES metode_pembayaran(id)
);

-- Fasilitas Table
CREATE TABLE fasilitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_fasilitas VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Staff Table
CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    work VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 3. Insert Default Data
-- Admin User
INSERT INTO users (username, password, email, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@hotelease.com', 'admin');

-- Sample Rooms
INSERT INTO kamar (tipe_kamar, harga, fasilitas, tersedia) VALUES 
('Deluxe Room', 500000.00, 'AC, TV, Wifi, Mini Bar', TRUE),
('Superior Room', 350000.00, 'AC, TV, Wifi', TRUE),
('Standard Room', 250000.00, 'AC, TV', TRUE);

-- Sample Payment Methods
INSERT INTO metode_pembayaran (nama_metode, deskripsi, nomor_rekening, atas_nama, status) VALUES 
('Bank BCA', 'Transfer Bank BCA', '1234567890', 'PT Hotel Ease', 'aktif'),
('Bank Mandiri', 'Transfer Bank Mandiri', '0987654321', 'PT Hotel Ease', 'aktif'),
('DANA', 'E-Wallet DANA', '081234567890', 'Hotel Ease', 'aktif');

-- Sample Facilities
INSERT INTO fasilitas (nama_fasilitas, deskripsi) VALUES 
('Swimming Pool', 'Kolam renang dengan air hangat'),
('Restaurant', 'Restoran dengan menu internasional'),
('Gym & Fitness', 'Pusat kebugaran 24 jam'),
('Spa & Wellness', 'Layanan spa dan pijat');

-- Sample Staff
INSERT INTO staff (name, work) VALUES 
('Ahmad Rizki', 'Manager'),
('Sari Dewi', 'Receptionist'),
('Budi Santoso', 'Housekeeping'),
('Maya Sari', 'Chef');
```

---

## ⚙️ System Configuration

### Step 1: Database Connection

Edit file `config/koneksi.php`:

```php
<?php
$host = 'localhost';        // Database host
$user = 'root';            // Database username
$pass = '';                // Database password (kosong untuk XAMPP default)
$db   = 'hotelease';       // Database name

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
```

### Step 2: File Permissions (Linux/Mac)

```bash
# Set proper permissions
chmod -R 755 /path/to/backup.putri/
chmod -R 777 /path/to/backup.putri/uploads/  # Jika ada folder uploads
```

### Step 3: PHP Configuration

Edit `php.ini` (optional):

```ini
# Increase limits for file uploads
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 60
memory_limit = 256M

# Enable required extensions
extension=mysqli
extension=pdo_mysql
extension=gd
```

---

## 🏃‍♂️ First Run

### Step 1: Access Application

```
http://localhost/backup.putri
```

### Step 2: Verify Installation

1. **Check Landing Page**
   - Pastikan halaman utama terbuka dengan baik
   - Tidak ada error message

2. **Test Database Connection**
   - Akses halaman login: `http://localhost/backup.putri/auth/login.php`
   - Jika muncul form login, database connection berhasil

3. **Admin Login**
   ```
   Username: admin
   Password: admin123
   ```

### Step 3: Initial Setup

1. **Login sebagai Admin**
2. **Setup Kamar**
   - Tambah tipe kamar
   - Upload gambar kamar
   - Set harga dan fasilitas

3. **Setup Metode Pembayaran**
   - Konfigurasi rekening bank
   - Enable/disable payment methods

4. **Setup Fasilitas Hotel**
   - Tambah fasilitas hotel
   - Upload gambar fasilitas

---

## 🔧 Troubleshooting

### Common Issues

#### 1. Database Connection Error

**Problem**: `Koneksi gagal: Access denied for user 'root'@'localhost'`

**Solution**:
```php
// Check config/koneksi.php
$user = 'root';
$pass = '';  // Pastikan password benar

// Atau buat user baru di MySQL
CREATE USER 'hotelease'@'localhost' IDENTIFIED BY 'password123';
GRANT ALL PRIVILEGES ON hotelease.* TO 'hotelease'@'localhost';
FLUSH PRIVILEGES;
```

#### 2. Page Not Found (404)

**Problem**: Halaman tidak ditemukan

**Solution**:
```bash
# Pastikan Apache running
http://localhost/xampp/  # XAMPP status page

# Check directory structure
backup.putri/
├── index.php  # Harus ada
├── config/
│   └── koneksi.php
└── ...
```

#### 3. PHP Errors

**Problem**: PHP syntax errors atau function not found

**Solution**:
```ini
# Check PHP version
php -v

# Enable error reporting (development only)
error_reporting = E_ALL
display_errors = On
```

#### 4. Upload Directory Issues

**Problem**: Tidak bisa upload gambar

**Solution**:
```bash
# Create upload directories
mkdir -p uploads/kamar
mkdir -p uploads/fasilitas
mkdir -p uploads/bukti_bayar

# Set permissions (Linux/Mac)
chmod -R 777 uploads/
```

#### 5. Session Issues

**Problem**: Login tidak tersimpan

**Solution**:
```php
// Check session configuration
session.auto_start = 0
session.use_cookies = 1
session.cookie_httponly = 1
```

### Debug Mode

Enable debug untuk development:

```php
// Tambah di awal file PHP
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

---

## ✅ Verification

### Post-Installation Checklist

- [ ] **Database Connection**: Login berhasil
- [ ] **Admin Access**: Dashboard admin terbuka
- [ ] **CRUD Operations**: Bisa tambah/edit/hapus data
- [ ] **User Registration**: User bisa register
- [ ] **Booking System**: Booking berhasil dibuat
- [ ] **File Upload**: Upload gambar berfungsi
- [ ] **Responsive Design**: Tampilan baik di mobile

### Test Scenarios

#### 1. Admin Functionality
```
1. Login admin → Dashboard
2. Kelola Kamar → CRUD operations
3. Kelola Booking → Update status
4. Kelola Pembayaran → Konfirmasi
5. Generate Reports → PDF export
```

#### 2. User Functionality
```
1. Register user baru
2. Login user → Dashboard
3. Browse kamar → Detail
4. Booking kamar → Konfirmasi
5. Upload bukti bayar
6. Lihat riwayat booking
```

### Performance Check

```bash
# Check XAMPP services
http://localhost/xampp/

# Check PHP info
http://localhost/backup.putri/phpinfo.php  # Create this file for debugging

# Monitor MySQL processes
SHOW PROCESSLIST;  # In phpMyAdmin SQL tab
```

---

## 📚 Next Steps

Setelah instalasi berhasil:

1. **Read Documentation**
   - [Usage Guide](Usage.md) - Cara menggunakan sistem
   - [Database Documentation](Database.md) - Schema database

2. **Customization**
   - Sesuaikan logo dan branding
   - Konfigurasi email settings
   - Setup backup scheduling

3. **Production Deployment**
   - Lihat [Deployment Guide](Deployment.md)
   - Setup SSL certificate
   - Configure production database

---

## 🆘 Need Help?

Jika mengalami kesulitan:

- 📧 **Email**: putrin151204@gmail.com
- 📖 **Documentation**: Baca file dokumentasi lainnya
- 🔍 **Check Logs**: Lihat error logs Apache/PHP
- 💬 **Community**: Forum XAMPP atau PHP community

---

**✅ Installation Complete!** 

Selamat! HotelEase sudah berhasil diinstall. Lanjutkan ke [Usage Guide](Usage.md) untuk mempelajari cara menggunakan sistem.

---

*Made with ❤️ by Putri Nabila Az Zahra - HotelEase Installation Guide*
