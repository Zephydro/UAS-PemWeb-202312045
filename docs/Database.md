# 📊 Database Documentation - HotelEase

## 📋 Overview

HotelEase menggunakan MySQL sebagai database management system dengan desain yang terstruktur untuk mengelola operasional hotel. Database ini terdiri dari 7 tabel utama yang saling berelasi untuk mendukung seluruh fungsionalitas sistem.

## 🏗️ Database Schema

### Database Information
- **Nama Database**: `hotelease`
- **Engine**: InnoDB
- **Charset**: utf8mb4
- **Collation**: utf8mb4_general_ci
- **Total Tables**: 7

## 📈 Entity Relationship Diagram (ERD)

```
┌─────────────────┐       ┌─────────────────┐       ┌─────────────────┐
│     USERS       │       │     BOOKING     │       │     KAMAR       │
├─────────────────┤       ├─────────────────┤       ├─────────────────┤
│ id (PK)         │◄─────┤│ user_id (FK)    │      ┌┤│ id (PK)         │
│ username        │       │ id (PK)         │      │ │ tipe_kamar      │
│ password        │       │ nama            │      │ │ harga           │
│ email           │       │ no_hp           │      │ │ fasilitas       │
│ role            │       │ tipe_kamar      │──────┘ │ tersedia        │
│ created_at      │       │ jumlah_tamu     │        │ gambar          │
└─────────────────┘       │ jumlah_malam    │        │ created_at      │
                          │ harga_total     │        └─────────────────┘
                          │ status          │
                          │ created_at      │
                          └─────────────────┘
                                    │
                                    ▼
                          ┌─────────────────┐       ┌─────────────────┐
                          │   PEMBAYARAN    │       │METODE_PEMBAYARAN│
                          ├─────────────────┤       ├─────────────────┤
                          │ id (PK)         │      ┌┤│ id (PK)         │
                          │ booking_id (FK) │──────┘ │ nama_metode     │
                          │ metode_pembayaran_id(FK)┤│ deskripsi       │
                          │ jumlah          │        │ nomor_rekening  │
                          │ status          │        │ atas_nama       │
                          │ bukti_bayar     │        │ status          │
                          │ tanggal_pembayaran      │ created_at      │
                          │ created_at      │        └─────────────────┘
                          └─────────────────┘

┌─────────────────┐       ┌─────────────────┐
│   FASILITAS     │       │     STAFF       │
├─────────────────┤       ├─────────────────┤
│ id (PK)         │       │ id (PK)         │
│ nama_fasilitas  │       │ name            │
│ deskripsi       │       │ work            │
│ gambar          │       │ created_at      │
│ created_at      │       └─────────────────┘
└─────────────────┘
```

## 🗂️ Tabel Detail

### 1. 👤 Tabel `users`
Menyimpan data pengguna sistem (admin dan customer).

```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'customer') DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT | Primary key, auto increment |
| `username` | VARCHAR(50) | Unique username untuk login |
| `password` | VARCHAR(255) | Hashed password |
| `email` | VARCHAR(100) | Email address (unique) |
| `role` | ENUM | Role pengguna: 'admin' atau 'customer' |
| `created_at` | TIMESTAMP | Waktu registrasi |

**Default Data:**
- Admin: username: `admin`, password: `admin123`
- Customer: dapat register melalui form

### 2. 🏨 Tabel `kamar`
Menyimpan informasi kamar hotel.

```sql
CREATE TABLE kamar (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipe_kamar VARCHAR(100) NOT NULL,
    harga DECIMAL(10,2) NOT NULL,
    fasilitas TEXT,
    tersedia BOOLEAN DEFAULT TRUE,
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT | Primary key, auto increment |
| `tipe_kamar` | VARCHAR(100) | Nama/tipe kamar |
| `harga` | DECIMAL(10,2) | Harga per malam |
| `fasilitas` | TEXT | Deskripsi fasilitas kamar |
| `tersedia` | BOOLEAN | Status ketersediaan |
| `gambar` | VARCHAR(255) | Path gambar kamar |
| `created_at` | TIMESTAMP | Waktu dibuat |

### 3. 📅 Tabel `booking`
Menyimpan data reservasi hotel.

```sql
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
```

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT | Primary key, auto increment |
| `user_id` | INT | Foreign key ke tabel users |
| `nama` | VARCHAR(100) | Nama pemesan |
| `no_hp` | VARCHAR(20) | Nomor HP pemesan |
| `tipe_kamar` | VARCHAR(100) | Tipe kamar yang dipesan |
| `jumlah_tamu` | INT | Jumlah tamu |
| `jumlah_malam` | INT | Jumlah malam menginap |
| `harga_total` | DECIMAL(10,2) | Total biaya |
| `status` | ENUM | Status booking |
| `created_at` | TIMESTAMP | Waktu booking |

### 4. 💳 Tabel `metode_pembayaran`
Menyimpan metode pembayaran yang tersedia.

```sql
CREATE TABLE metode_pembayaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_metode VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    nomor_rekening VARCHAR(50),
    atas_nama VARCHAR(100),
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT | Primary key, auto increment |
| `nama_metode` | VARCHAR(100) | Nama metode pembayaran |
| `deskripsi` | TEXT | Deskripsi metode |
| `nomor_rekening` | VARCHAR(50) | Nomor rekening/akun |
| `atas_nama` | VARCHAR(100) | Nama pemilik rekening |
| `status` | ENUM | Status aktif/nonaktif |
| `created_at` | TIMESTAMP | Waktu dibuat |

### 5. 💰 Tabel `pembayaran`
Menyimpan data pembayaran booking.

```sql
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
```

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT | Primary key, auto increment |
| `booking_id` | INT | Foreign key ke tabel booking |
| `metode_pembayaran_id` | INT | Foreign key ke tabel metode_pembayaran |
| `jumlah` | DECIMAL(10,2) | Jumlah pembayaran |
| `status` | ENUM | Status pembayaran |
| `bukti_bayar` | VARCHAR(255) | Path file bukti bayar |
| `tanggal_pembayaran` | TIMESTAMP | Waktu pembayaran |
| `created_at` | TIMESTAMP | Waktu dibuat |

### 6. 🏢 Tabel `fasilitas`
Menyimpan data fasilitas hotel.

```sql
CREATE TABLE fasilitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_fasilitas VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    gambar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT | Primary key, auto increment |
| `nama_fasilitas` | VARCHAR(100) | Nama fasilitas |
| `deskripsi` | TEXT | Deskripsi fasilitas |
| `gambar` | VARCHAR(255) | Path gambar fasilitas |
| `created_at` | TIMESTAMP | Waktu dibuat |

### 7. 👥 Tabel `staff`
Menyimpan data karyawan hotel.

```sql
CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    work VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

| Field | Type | Description |
|-------|------|-------------|
| `id` | INT | Primary key, auto increment |
| `name` | VARCHAR(100) | Nama karyawan |
| `work` | VARCHAR(100) | Jabatan/pekerjaan |
| `created_at` | TIMESTAMP | Waktu dibuat |

## 🔗 Relasi Antar Tabel

### Primary Relationships

1. **users → booking** (One-to-Many)
   - Satu user dapat memiliki banyak booking
   - Foreign Key: `booking.user_id` → `users.id`

2. **booking → pembayaran** (One-to-Many)
   - Satu booking dapat memiliki banyak pembayaran
   - Foreign Key: `pembayaran.booking_id` → `booking.id`

3. **metode_pembayaran → pembayaran** (One-to-Many)
   - Satu metode pembayaran dapat digunakan untuk banyak pembayaran
   - Foreign Key: `pembayaran.metode_pembayaran_id` → `metode_pembayaran.id`

### Referential Integrity

- **CASCADE DELETE**: Ketika user dihapus, semua booking terkait akan terhapus
- **CASCADE DELETE**: Ketika booking dihapus, semua pembayaran terkait akan terhapus
- **RESTRICT**: Metode pembayaran tidak dapat dihapus jika masih digunakan

## 📊 Indexes

### Primary Keys
- Setiap tabel memiliki PRIMARY KEY pada kolom `id`
- AUTO_INCREMENT untuk semua primary key

### Unique Keys
- `users.username` - UNIQUE
- `users.email` - UNIQUE

### Foreign Key Indexes
- `booking.user_id` - INDEX
- `pembayaran.booking_id` - INDEX
- `pembayaran.metode_pembayaran_id` - INDEX

## 🔒 Security & Constraints

### Data Validation
- **NOT NULL constraints** pada field yang wajib diisi
- **ENUM constraints** untuk membatasi nilai tertentu
- **Email validation** di level aplikasi
- **Password hashing** menggunakan PHP `password_hash()`

### Data Types
- **VARCHAR** dengan batas yang sesuai untuk mencegah overflow
- **DECIMAL(10,2)** untuk nilai mata uang
- **TIMESTAMP** untuk tracking waktu dengan timezone
- **BOOLEAN** untuk flag status

## 📈 Performance Considerations

### Optimization
- **Primary keys** pada semua tabel untuk faster access
- **Foreign key indexes** untuk faster JOIN operations
- **Proper data types** untuk efficient storage
- **ENUM types** untuk status fields

### Query Patterns
- JOIN queries untuk relational data
- WHERE clauses pada indexed columns
- ORDER BY pada timestamp fields for chronological data
- COUNT aggregations for dashboard metrics

## 🔄 Sample Queries

### User Authentication
```sql
SELECT id, username, role FROM users 
WHERE username = ? AND password = ?;
```

### Booking with Payment Status
```sql
SELECT b.*, p.status as payment_status, mp.nama_metode
FROM booking b
LEFT JOIN pembayaran p ON b.id = p.booking_id
LEFT JOIN metode_pembayaran mp ON p.metode_pembayaran_id = mp.id
WHERE b.user_id = ?;
```

### Revenue Analytics
```sql
SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    SUM(harga_total) as revenue,
    COUNT(*) as bookings
FROM booking 
WHERE status = 'diterima'
GROUP BY DATE_FORMAT(created_at, '%Y-%m')
ORDER BY month DESC;
```

### Payment Method Statistics
```sql
SELECT 
    mp.nama_metode,
    COUNT(p.id) as transaction_count,
    SUM(p.jumlah) as total_amount
FROM pembayaran p
JOIN metode_pembayaran mp ON p.metode_pembayaran_id = mp.id
WHERE p.status = 'dibayar'
GROUP BY mp.id
ORDER BY transaction_count DESC;
```

## 🚀 Database Setup

### Prerequisites
- MySQL 8.0+
- PHP MySQLi/PDO extension

### Installation Steps

1. **Create Database**
```sql
CREATE DATABASE hotelease 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_general_ci;
```

2. **Import Schema**
```bash
mysql -u root -p hotelease < database/schema.sql
```

3. **Insert Sample Data**
```bash
mysql -u root -p hotelease < database/sample_data.sql
```

## 🔧 Maintenance

### Backup Strategy
```bash
# Daily backup
mysqldump -u root -p hotelease > backup_$(date +%Y%m%d).sql

# Restore backup
mysql -u root -p hotelease < backup_20240101.sql
```

### Performance Monitoring
- Monitor slow queries with `SHOW PROCESSLIST`
- Use `EXPLAIN` for query optimization
- Regular `OPTIMIZE TABLE` for maintenance

---

**📝 Note**: Database ini dirancang untuk mendukung operasional hotel kecil hingga menengah dengan proyeksi pertumbuhan data yang scalable.

**🔗 Related Documentation**: 
- [Installation Guide](Installation.md)
- [Usage Documentation](Usage.md)
- [Deployment Guide](Deployment.md)
