# 📋 Usage Guide - HotelEase

## 📑 Daftar Isi

1. [Overview](#overview)
2. [Login & Authentication](#login--authentication)
3. [Admin Panel](#admin-panel)
4. [User Panel](#user-panel)
5. [Common Features](#common-features)
6. [Troubleshooting](#troubleshooting)
7. [Tips & Best Practices](#tips--best-practices)

---

## 🎯 Overview

HotelEase adalah sistem manajemen hotel dengan dua level akses utama:

- **👨‍💼 Administrator** - Mengelola seluruh operasional hotel
- **👤 Customer/User** - Melakukan booking dan melihat informasi

### Default Login Credentials

```
Admin:
Username: admin
Password: admin123

Customer:
Register sendiri melalui form registrasi
```

---

## 🔐 Login & Authentication

### 🚪 Akses Sistem

```
Landing Page: http://localhost/backup.putri
Login Page: http://localhost/backup.putri/auth/login.php
Register Page: http://localhost/backup.putri/auth/register.php
```

### 📝 Registration Process

1. **Buka Halaman Register**
   - Klik "Register" di landing page
   - Atau akses langsung `/auth/register.php`

2. **Isi Form Registrasi**
   ```
   Username: [unique username]
   Email: [valid email address]
   Password: [minimum 6 characters]
   Confirm Password: [must match]
   ```

3. **Submit & Auto Login**
   - Setelah registrasi berhasil, otomatis login
   - Redirect ke user dashboard

### 🔓 Login Process

1. **Pilih Form Login**
   - Username atau Email
   - Password

2. **Role Detection**
   - Admin → Redirect ke `/admin/dashboard.php`
   - Customer → Redirect ke `/user/dashboard.php`

3. **Session Management**
   - Session timeout: 24 jam (default)
   - Logout otomatis jika inaktif

---

## 👨‍💼 Admin Panel

### 🏠 Dashboard Admin

**URL**: `/admin/dashboard.php`

#### Features Overview
- **📊 Real-time Statistics**
  - Total Booking (hari ini)
  - Total Pendapatan (bulan ini)
  - Total Pembayaran Pending
  - Occupancy Rate

- **📈 Analytics Charts**
  - Pendapatan per bulan (Chart.js)
  - Tren booking bulanan
  - Status booking breakdown

- **🔄 Quick Actions**
  - Booking terbaru (5 terakhir)
  - Pembayaran pending
  - Link cepat ke semua modul

### 🏨 Manajemen Kamar

**URL**: `/admin/kamar.php`

#### 📋 Daftar Kamar
- **View**: Table dengan pagination
- **Columns**: ID, Tipe Kamar, Harga, Status, Actions
- **Actions**: Edit, Delete, Toggle Status

#### ➕ Tambah Kamar Baru
1. **Klik "Tambah Kamar"**
2. **Isi Form**:
   ```
   Tipe Kamar: [e.g., "Deluxe Room"]
   Harga: [e.g., 500000.00]
   Fasilitas: [deskripsi lengkap]
   Upload Gambar: [jpg, png, gif - max 2MB]
   Status: [Tersedia/Tidak Tersedia]
   ```
3. **Submit**: Data tersimpan otomatis

#### ✏️ Edit Kamar
1. **Klik Icon Edit** di tabel
2. **Update Data**: Semua field dapat diubah
3. **Ganti Gambar**: Upload gambar baru (optional)
4. **Save Changes**

#### 🗑️ Hapus Kamar
- Klik icon delete
- Konfirmasi penghapusan
- **Warning**: Tidak bisa dihapus jika ada booking aktif

### 📅 Manajemen Booking

**URL**: `/admin/booking_admin.php`

#### 📊 Dashboard Booking
- **Filter Status**: All, Pending, Diterima, Ditolak, Selesai
- **Search**: Berdasarkan nama atau nomor HP
- **Export**: PDF report

#### ✅ Proses Booking
1. **Review Booking Details**:
   - Nama tamu
   - Kontak (HP/Email)
   - Tipe kamar & jumlah malam
   - Total harga
   - Tanggal booking

2. **Update Status**:
   - **Pending** → Terima/Tolak
   - **Diterima** → Selesai (setelah check-out)
   - **Ditolak** → Set alasan penolakan

3. **Actions Available**:
   - View Detail
   - Update Status
   - Print Voucher
   - Contact Guest

### 💳 Manajemen Pembayaran

**URL**: `/admin/pembayaran.php`

#### 🔍 Review Pembayaran
- **Filter**: Status, Tanggal, Metode
- **View**: Bukti transfer yang diupload
- **Verify**: Konfirmasi pembayaran

#### ✅ Konfirmasi Pembayaran
1. **Check Bukti Transfer**
   - View uploaded image
   - Verify amount & details
   - Check account details

2. **Update Status**:
   - **Pending** → Dibayar/Gagal
   - **Dibayar** → Final status
   - **Gagal** → Minta upload ulang

3. **Notification**:
   - Auto update booking status
   - Send confirmation (if email enabled)

### 💰 Metode Pembayaran

**URL**: `/admin/metode_pembayaran.php`

#### 🏦 Kelola Payment Methods
1. **Add New Method**:
   ```
   Nama Metode: [e.g., "Bank BCA"]
   Deskripsi: [instruksi pembayaran]
   Nomor Rekening: [account number]
   Atas Nama: [account holder]
   Status: [Aktif/Nonaktif]
   ```

2. **Edit Existing**: Update details rekening
3. **Toggle Status**: Enable/disable method

### 🏢 Manajemen Fasilitas

**URL**: `/admin/fasilitas.php`

#### 🌟 CRUD Fasilitas
1. **Add Facility**:
   ```
   Nama Fasilitas: [e.g., "Swimming Pool"]
   Deskripsi: [detail fasilitas]
   Upload Gambar: [foto fasilitas]
   ```

2. **Display**: Grid view dengan gambar
3. **Edit/Delete**: Standard CRUD operations

### 👥 Manajemen Staff

**URL**: `/admin/staff.php`

#### 👤 Kelola Karyawan
1. **Add Staff**:
   ```
   Nama: [nama lengkap]
   Pekerjaan: [jabatan/divisi]
   ```

2. **List View**: Tabel dengan foto (jika ada)
3. **Edit/Delete**: Update data staff

### 👨‍👩‍👧‍👦 Manajemen Users

**URL**: `/admin/users.php`

#### 🔒 User Management
- **View All Users**: Admin & Customer
- **Edit User**: Update profile & role
- **Delete User**: Remove account
- **Reset Password**: Admin can reset user passwords

### 📊 Reports & Analytics

**URL**: `/admin/reports.php`

#### 📈 Generate Reports
1. **Booking Report**:
   - Filter by date range
   - Status breakdown
   - Revenue summary

2. **Payment Report**:
   - Payment method statistics
   - Success rate analysis
   - Monthly trends

3. **PDF Export**:
   - Professional layout
   - Charts & graphs
   - Print-ready format

---

## 👤 User Panel

### 🏠 User Dashboard

**URL**: `/user/dashboard.php`

#### 🎯 Quick Overview
- **My Bookings**: Recent booking history
- **Payment Status**: Outstanding payments
- **Quick Book**: Link to new booking
- **Profile**: View/edit profile

### 🏨 Browse Kamar

**URL**: `/user/kamar.php`

#### 🔍 Room Catalog
- **Grid View**: Gambar, harga, fasilitas
- **Filter**: By price range, facilities
- **Detail View**: Klik untuk detail lengkap
- **Book Now**: Direct booking button

### 📅 Booking Kamar

**URL**: `/user/booking.php`

#### 📝 Booking Process
1. **Select Room Type**: Dari catalog atau dropdown
2. **Guest Information**:
   ```
   Nama Lengkap: [nama tamu]
   Nomor HP: [kontak utama]
   Email: [optional]
   ```

3. **Booking Details**:
   ```
   Check-in Date: [tanggal masuk]
   Jumlah Malam: [1-30 hari]
   Jumlah Tamu: [1-4 orang]
   ```

4. **Price Calculation**:
   - Harga per malam × jumlah malam
   - Auto-calculate total
   - Show breakdown

5. **Submit Booking**:
   - Review details
   - Confirm booking
   - Redirect to payment

### 💳 Proses Pembayaran

**URL**: `/user/transaksi.php`

#### 💰 Payment Flow
1. **Select Payment Method**:
   - Bank Transfer (BCA, Mandiri, BNI)
   - E-Wallet (DANA, OVO, GoPay)
   - View account details

2. **Upload Bukti Bayar**:
   ```
   File Type: JPG, PNG, PDF
   Max Size: 2MB
   Requirements: Clear, readable receipt
   ```

3. **Track Status**:
   - Pending → Processing → Paid
   - Real-time status updates
   - Estimated verification time

### 📜 Riwayat Booking

**URL**: `/user/riwayat.php`

#### 📊 Booking History
- **All Bookings**: Chronological list
- **Status Filter**: Pending, Confirmed, Completed
- **Details**: View full booking information
- **Actions**: 
  - Download voucher
  - Cancel booking (if allowed)
  - Rate & review (future feature)

### 👤 Profile Management

**URL**: `/user/profil.php`

#### 🔧 Account Settings
1. **Personal Information**:
   ```
   Username: [cannot change]
   Email: [update email]
   Phone: [add/update phone]
   ```

2. **Change Password**:
   ```
   Current Password: [verify identity]
   New Password: [minimum 6 chars]
   Confirm Password: [must match]
   ```

3. **Profile Picture**: Upload avatar (future feature)

---

## 🔧 Common Features

### 🔍 Search & Filter

#### 📊 Global Search
- **Admin**: Search across all modules
- **User**: Search rooms, bookings
- **Filters**: Date range, status, category

#### 🎯 Advanced Filters
```
Date Range: [from date] - [to date]
Status: [dropdown options]
Price Range: [min] - [max]
Category: [room type, payment method]
```

### 📱 Responsive Design

#### 📲 Mobile Optimization
- **Breakpoints**: 
  - Mobile: < 768px
  - Tablet: 768px - 1024px
  - Desktop: > 1024px

- **Touch-Friendly**: Large buttons, easy navigation
- **Performance**: Optimized images, lazy loading

### 🔒 Security Features

#### 🛡️ Authentication
- **Session Timeout**: 24 hours inactivity
- **CSRF Protection**: Form tokens
- **SQL Injection**: Prepared statements
- **XSS Protection**: Input sanitization

#### 🔐 Role-Based Access
```
Admin Access:
- All admin/* pages
- CRUD operations
- Reports & analytics

User Access:  
- User/* pages only
- Own booking data
- Profile management
```

### 💾 Data Management

#### 📤 Export Features
- **PDF Reports**: Professional format
- **Excel Export**: Raw data (future)
- **Print Views**: Optimized layouts

#### 🔄 Backup & Restore
- **Database Backup**: Manual/scheduled
- **File Backup**: Images & uploads
- **Restore Points**: Version control

---

## 🔧 Troubleshooting

### ❌ Common Issues

#### 1. Login Problems
**Issue**: Cannot login with correct credentials

**Solutions**:
```php
// Check session configuration
session_start();
if (!isset($_SESSION['user_id'])) {
    // Session expired
}

// Clear browser cache
// Check database connection
// Verify password hash
```

#### 2. Upload Failures
**Issue**: Cannot upload images

**Solutions**:
```bash
# Check folder permissions
chmod 777 uploads/
chmod 777 uploads/kamar/
chmod 777 uploads/bukti_bayar/

# Check PHP limits
upload_max_filesize = 10M
post_max_size = 10M
```

#### 3. Database Errors
**Issue**: MySQL connection failed

**Solutions**:
```php
// Check config/koneksi.php
$host = 'localhost';
$user = 'root';
$pass = '';  // Verify password
$db = 'hotelease';  // Verify database name

// Test connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
```

#### 4. Payment Status Not Updating
**Issue**: Payment remains pending

**Solutions**:
1. **Check Upload**:
   - File uploaded successfully?
   - Correct file format?
   - File size within limits?

2. **Admin Action**:
   - Admin must manually verify
   - Update payment status
   - Check booking status sync

#### 5. Booking Conflicts
**Issue**: Double booking or unavailable rooms

**Solutions**:
```sql
-- Check room availability
SELECT * FROM kamar WHERE tersedia = 1;

-- Check conflicting bookings
SELECT * FROM booking 
WHERE tipe_kamar = 'Deluxe Room' 
AND status IN ('pending', 'diterima');
```

### 🔧 Performance Issues

#### 🐌 Slow Loading
1. **Database Optimization**:
   ```sql
   -- Add indexes
   CREATE INDEX idx_booking_user ON booking(user_id);
   CREATE INDEX idx_booking_status ON booking(status);
   ```

2. **Image Optimization**:
   - Compress images before upload
   - Use WebP format
   - Implement lazy loading

3. **Caching**:
   - Browser caching headers
   - Database query caching
   - Static file caching

---

## 💡 Tips & Best Practices

### 👨‍💼 For Administrators

#### 📊 Daily Operations
1. **Morning Checklist**:
   - [ ] Check new bookings
   - [ ] Verify pending payments
   - [ ] Review room availability
   - [ ] Check system alerts

2. **Booking Management**:
   - Respond to bookings within 24 hours
   - Verify payment proofs carefully
   - Keep room status updated
   - Maintain communication log

3. **Data Management**:
   - Regular database backups
   - Update room prices seasonally
   - Maintain accurate facility info
   - Archive old bookings

#### 📈 Performance Monitoring
```bash
# Daily checks
- Server disk space
- Database size
- Upload folder size
- Error logs review
```

### 👤 For Customers

#### 📝 Booking Tips
1. **Before Booking**:
   - Check room availability
   - Read facility descriptions
   - Understand cancellation policy
   - Save payment method details

2. **Payment Process**:
   - Upload clear payment proof
   - Keep transaction receipt
   - Follow up if status pending > 24h
   - Contact admin for questions

3. **Profile Management**:
   - Keep contact info updated
   - Use strong passwords
   - Logout from shared devices
   - Save booking confirmations

### 🔒 Security Best Practices

#### 🛡️ System Security
```php
// Input validation
$input = filter_input(INPUT_POST, 'data', FILTER_SANITIZE_STRING);

// Password hashing
$hashed = password_hash($password, PASSWORD_DEFAULT);

// Prepared statements
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
```

#### 🔐 User Security
- Use unique, strong passwords
- Enable two-factor authentication (if available)
- Regular password updates
- Secure logout practices
- Monitor account activity

### 📱 Mobile Usage

#### 📲 Mobile-First Tips
1. **Navigation**:
   - Use sidebar menu
   - Swipe gestures
   - Touch-friendly buttons

2. **Data Entry**:
   - Use device keyboard features
   - Auto-complete suggestions
   - Voice input (if supported)

3. **Image Uploads**:
   - Camera integration
   - Gallery selection
   - Image compression

---

## 🆘 Getting Help

### 📞 Support Channels

1. **Technical Issues**:
   - 📧 Email: putrin151204@gmail.com
   - 📖 Check documentation
   - 🔍 Search troubleshooting section

2. **Feature Requests**:
   - Submit feedback form
   - Document enhancement needs
   - Provide use case examples

3. **Bug Reports**:
   - Describe steps to reproduce
   - Include error messages
   - Provide system information

### 📚 Additional Resources

- [Installation Guide](Installation.md) - Setup instructions
- [Database Documentation](Database.md) - Schema details
- [Deployment Guide](Deployment.md) - Production setup

---

## 📊 Quick Reference

### 🔑 Keyboard Shortcuts
```
Ctrl + / : Quick search
Ctrl + S : Save form (where applicable)
Esc : Close modal/popup
Tab : Navigate form fields
```

### 📅 Important URLs
```
Landing: /
Admin Login: /auth/login.php
User Dashboard: /user/dashboard.php
Admin Dashboard: /admin/dashboard.php
Booking: /user/booking.php
Payment: /user/transaksi.php
```

### 🎯 Status Codes
```
Booking Status:
- pending: Menunggu konfirmasi admin
- diterima: Booking dikonfirmasi
- ditolak: Booking ditolak
- selesai: Check-out completed

Payment Status:
- pending: Menunggu verifikasi
- dibayar: Payment verified
- gagal: Payment failed/rejected
```

---

**✅ You're ready to use HotelEase!**

Sistem ini dirancang untuk kemudahan penggunaan. Jika ada pertanyaan, jangan ragu untuk menghubungi support team.

---

*Made with ❤️ by Putri Nabila Az Zahra - HotelEase Usage Guide*
