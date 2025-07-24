-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Jul 2025 pada 20.20
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

-- Membuat database hotelease jika belum ada
CREATE DATABASE IF NOT EXISTS `hotelease` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- Menggunakan database hotelease
USE `hotelease`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hotelease`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `booking`
--

CREATE TABLE `booking` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `tipe_kamar` varchar(50) DEFAULT NULL,
  `jumlah_tamu` int(11) DEFAULT NULL,
  `jumlah_malam` int(11) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `harga_total` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('diajukan','diverifikasi','ditolak','diterima') DEFAULT 'diajukan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `booking`
--

INSERT INTO `booking` (`id`, `user_id`, `nama`, `no_hp`, `tipe_kamar`, `jumlah_tamu`, `jumlah_malam`, `catatan`, `harga_total`, `created_at`, `status`) VALUES
(1, 2, 'Demo User', '081234567899', 'Deluxe Room', 2, 3, 'Demo booking for testing', 1950000, '2025-07-20 10:00:00', 'ditolak'),
(2, 2, 'sad1212121', '1212', 'luxxury', 1, 2, '0', 24, '2025-07-20 08:55:29', 'diverifikasi'),
(3, 2, 'sdsd', '2323', 'luxxury', 3, 3, '0', 36, '2025-07-21 16:14:04', 'diajukan'),
(4, 2, 'sdsd', '2323', 'luxxury', 3, 3, '0', 36, '2025-07-21 16:18:16', 'diajukan'),
(5, 2, 'sdsd', '2323', 'luxxury', 3, 3, '0', 36, '2025-07-21 16:24:06', 'diajukan'),
(6, 2, 'sdsd', '2323', 'luxxury', 3, 3, '0', 36, '2025-07-21 16:25:57', ''),
(7, 2, 'fs', '3434', 'luxxury', 3, 5, '0', 60, '2025-07-23 18:15:26', '');

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `booking_summary_view`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `booking_summary_view` (
`id` int(11)
,`nama` varchar(100)
,`no_hp` varchar(20)
,`tipe_kamar` varchar(50)
,`jumlah_tamu` int(11)
,`jumlah_malam` int(11)
,`harga_total` int(11)
,`status` enum('diajukan','diverifikasi','ditolak','diterima')
,`created_at` timestamp
,`user_nama` varchar(100)
,`user_email` varchar(100)
);

-- --------------------------------------------------------

--
-- Struktur dari tabel `fasilitas`
--

CREATE TABLE `fasilitas` (
  `id` int(11) NOT NULL,
  `nama_fasilitas` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `harga_tambahan` decimal(10,2) DEFAULT 0.00,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `fasilitas`
--

INSERT INTO `fasilitas` (`id`, `nama_fasilitas`, `deskripsi`, `harga_tambahan`, `status`) VALUES
(1, 'Kolam Renang', '✅ Outdoor\n✅ Buka pukul 06.00 – 20.00\n✅ Asri', 0.00, 'aktif'),
(2, 'Gym', '✅ Alat lengkap: Dumbbell, Treadmill\n✅ Akses 24 jam', 50000.00, 'aktif'),
(3, 'Taman', '✅ Taman hijau & sejuk\n✅ Tempat bersantai dan foto', 0.00, 'aktif'),
(4, 'Spa & Massage', '✅ Relaksasi tubuh\n✅ Terapis profesional', 150000.00, 'aktif'),
(5, 'Breakfast Premium', '✅ Buffet lengkap\n✅ Menu internasional', 75000.00, 'aktif'),
(6, 'ya gitu', 'sdnjsdfnsdj', 12222222.00, 'aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `aktivitas` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `tanggal` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `log_aktivitas`
--

INSERT INTO `log_aktivitas` (`id`, `user_id`, `aktivitas`, `deskripsi`, `ip_address`, `tanggal`) VALUES
(1, 1, 'Login Gagal', 'Percobaan login dengan password salah', '::1', '2025-07-20 07:50:59'),
(2, 1, 'Login Gagal', 'Percobaan login dengan password salah', '::1', '2025-07-20 07:51:32'),
(3, 1, 'Login Gagal', 'Percobaan login dengan password salah', '::1', '2025-07-20 07:51:38'),
(4, 1, 'Login Gagal', 'Percobaan login dengan password salah', '::1', '2025-07-20 07:55:07'),
(5, 1, 'Login', 'User berhasil login ke sistem', '::1', '2025-07-20 07:55:13'),
(6, 1, 'Logout', 'User berhasil logout dari sistem', '::1', '2025-07-20 07:57:00'),
(7, 2, 'Login Gagal', 'Percobaan login dengan password salah', '::1', '2025-07-20 07:57:18'),
(8, 2, 'Login Gagal', 'Percobaan login dengan password salah', '::1', '2025-07-20 07:57:23'),
(9, 2, 'Login', 'User berhasil login ke sistem', '::1', '2025-07-20 07:57:41');

-- --------------------------------------------------------

--
-- Struktur dari tabel `metode_pembayaran`
--

CREATE TABLE `metode_pembayaran` (
  `id` int(11) NOT NULL,
  `nama_metode` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `nomor_rekening` varchar(50) DEFAULT NULL,
  `atas_nama` varchar(100) DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `metode_pembayaran`
--

INSERT INTO `metode_pembayaran` (`id`, `nama_metode`, `deskripsi`, `nomor_rekening`, `atas_nama`, `status`) VALUES
(1, 'Transfer Bank BCA', 'Transfer ke rekening BCA', '1234567890', 'Hotel Ease', 'aktif'),
(2, 'Transfer Bank Mandiri', 'Transfer ke rekening Mandiri', '0987654321', 'Hotel Ease', 'aktif'),
(3, 'E-Wallet DANA', 'Pembayaran melalui DANA', '081234567890', 'Hotel Ease', 'aktif'),
(4, 'E-Wallet OVO', 'Pembayaran melalui OVO', '081234567890', 'Hotel Ease', 'aktif'),
(5, 'Cash', 'Pembayaran tunai di hotel', NULL, NULL, 'aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `judul` varchar(255) NOT NULL,
  `pesan` text DEFAULT NULL,
  `tipe` enum('info','success','warning','error') DEFAULT 'info',
  `status` enum('belum_dibaca','dibaca') DEFAULT 'belum_dibaca',
  `tanggal` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `notifikasi`
--

INSERT INTO `notifikasi` (`id`, `user_id`, `judul`, `pesan`, `tipe`, `status`, `tanggal`) VALUES
(1, 2, 'Selamat Datang!', 'Selamat datang di HotelEase. Terima kasih telah bergabung dengan kami.', 'info', 'belum_dibaca', '2025-07-20 08:25:35'),
(2, 2, 'Selamat Datang!', 'Selamat datang di HotelEase. Terima kasih telah bergabung dengan kami.', 'info', 'belum_dibaca', '2025-07-20 08:48:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `metode_pembayaran_id` int(11) DEFAULT NULL,
  `jumlah` decimal(10,2) DEFAULT NULL,
  `bukti_pembayaran` varchar(255) DEFAULT NULL,
  `keterangan_pembayaran` text DEFAULT NULL,
  `tanggal_upload` datetime DEFAULT NULL,
  `status` enum('belum_dibayar','menunggu_verifikasi','dibayar','gagal') DEFAULT 'belum_dibayar',
  `tanggal_pembayaran` timestamp NULL DEFAULT current_timestamp(),
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `booking_id`, `metode_pembayaran_id`, `jumlah`, `bukti_pembayaran`, `keterangan_pembayaran`, `tanggal_upload`, `status`, `tanggal_pembayaran`, `verified_by`, `verified_at`) VALUES
(1, 2, 3, 24.00, NULL, NULL, NULL, 'belum_dibayar', '2025-07-20 08:55:29', NULL, NULL),
(2, 3, 5, 36.00, NULL, NULL, NULL, 'belum_dibayar', '2025-07-21 16:14:04', NULL, NULL),
(3, 4, 5, 36.00, NULL, NULL, NULL, 'belum_dibayar', '2025-07-21 16:18:16', NULL, NULL),
(4, 5, 5, 36.00, NULL, NULL, NULL, 'belum_dibayar', '2025-07-21 16:24:06', NULL, NULL),
(5, 6, 5, 36.00, 'bukti_6_1753115180_687e6a2c57537.png', '', '2025-07-22 00:26:20', 'gagal', '2025-07-21 16:25:57', 1, '2025-07-23 06:26:50'),
(6, 7, 5, 60.00, 'bukti_7_1753294552_688126d841a1c.png', '', '2025-07-24 02:15:52', 'menunggu_verifikasi', '2025-07-23 18:15:26', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nama_role` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `roles`
--

INSERT INTO `roles` (`id`, `nama_role`, `deskripsi`) VALUES
(1, 'admin', 'Administrator sistem dengan akses penuh'),
(2, 'user', 'Tamu hotel');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `nomor_kamar` varchar(10) NOT NULL,
  `tipe_kamar` varchar(50) NOT NULL,
  `harga_per_malam` decimal(10,2) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `kapasitas` int(11) DEFAULT 2,
  `status` enum('tersedia','terisi','maintenance') DEFAULT 'tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `rooms`
--

INSERT INTO `rooms` (`id`, `nomor_kamar`, `tipe_kamar`, `harga_per_malam`, `deskripsi`, `foto`, `kapasitas`, `status`) VALUES
(2, '1', 'Superior Room', 450000.00, '✅ Pegunungan, 1-2 Tamu\n✅ Balkon, TV', 'superior.jpg', 2, 'tersedia'),
(3, '2', 'Deluxe Room', 650000.00, '✅ Taman, 2-3 Tamu\n✅ Bathtub, AC', 'deluxe.jpg', 3, 'tersedia'),
(4, '3', 'Premium Room', 950000.00, '✅ Suite, 3-4 Tamu\r\n✅ Lounge, Breakfast', 'premium.jpg', 4, 'tersedia'),
(5, 'sds', 'luxxury', 12.00, 'ya gitu', 'Untitled (1).png', 2, 'tersedia'),
(6, '12', 'sekian', 12.00, 'rhhr', '1.jpg', 2, 'tersedia');

-- --------------------------------------------------------

--
-- Struktur dari tabel `room_fasilitas`
--

CREATE TABLE `room_fasilitas` (
  `room_id` int(11) NOT NULL,
  `fasilitas_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `room_fasilitas`
--

INSERT INTO `room_fasilitas` (`room_id`, `fasilitas_id`) VALUES
(2, 1),
(2, 3),
(3, 1),
(3, 2),
(3, 3),
(4, 1),
(4, 2),
(4, 3),
(4, 4),
(4, 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `security_sessions`
--

CREATE TABLE `security_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `work` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `status` enum('aktif','nonaktif') DEFAULT 'aktif'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `staff`
--

INSERT INTO `staff` (`id`, `name`, `work`, `email`, `phone`, `salary`, `hire_date`, `status`) VALUES
(1, 'Putri Nabila', 'Manager', 'putri@hotel.com', '081234567890', 8000000.00, '2024-01-15', 'aktif'),
(3, 'Rohit Patel', 'Cook', 'rohit@hotel.com', '081234567891', 4500000.00, '2024-02-01', 'aktif'),
(4, 'Dipak', 'Cook', 'dipak@hotel.com', '081234567892', 4500000.00, '2024-02-01', 'aktif'),
(5, 'Tirth', 'Helper', 'tirth@hotel.com', '081234567893', 3000000.00, '2024-03-01', 'aktif'),
(6, 'Mohan', 'Helper', 'mohan@hotel.com', '081234567894', 3000000.00, '2024-03-01', 'aktif'),
(7, 'Shyam', 'Cleaner', 'shyam@hotel.com', '081234567895', 3500000.00, '2024-03-15', 'aktif'),
(8, 'Rohan', 'Receptionist', 'rohan@hotel.com', '081234567896', 4000000.00, '2024-04-01', 'aktif'),
(9, 'Hiren', 'Security', 'hiren@hotel.com', '081234567897', 3800000.00, '2024-04-01', 'aktif'),
(11, 'Rekha', 'Cook', 'rekha@hotel.com', '081234567898', 4500000.00, '2024-05-01', 'aktif'),
(13, 'Kazuha', 'Cleaner', 'kazuha@hotel.com', '081234567899', 3500000.00, '2024-06-01', 'aktif'),
(14, 'Zephydro', 'Manager', 'zeph@hotel.com', '081234567800', 8000000.00, '2024-06-15', 'aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `testimoni`
--

CREATE TABLE `testimoni` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `room_id` int(11) DEFAULT NULL,
  `komentar` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `tanggal` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `role_id` int(11) NOT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `password_reset_token` varchar(255) DEFAULT NULL,
  `password_reset_expires` timestamp NULL DEFAULT NULL,
  `login_attempts` int(11) DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `nama_lengkap`, `email`, `phone`, `alamat`, `role_id`, `last_login`, `created_at`, `password_reset_token`, `password_reset_expires`, `login_attempts`, `locked_until`) VALUES
(1, 'admin', 'admin123', 'Admin Hotel', 'admin@hoteleasy.com', '081234567890', 'Hotel Ease, Jakarta', 1, NULL, '2025-07-16 17:35:13', NULL, NULL, 0, NULL),
(2, 'user', 'user123', 'Demo User', 'user@hoteleasy.com', '081234567899', 'Jakarta, Indonesia', 2, NULL, '2025-07-20 10:00:00', NULL, NULL, 0, NULL),
(3, 'awa', '123456', 'sdsd', 'sekian@gmail.com', NULL, NULL, 2, NULL, '2025-07-21 14:58:45', NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Stand-in struktur untuk tampilan `user_auth_view`
-- (Lihat di bawah untuk tampilan aktual)
--
CREATE TABLE `user_auth_view` (
`id` int(11)
,`username` varchar(50)
,`password` varchar(255)
,`nama_lengkap` varchar(100)
,`email` varchar(100)
,`role_id` int(11)
,`nama_role` varchar(50)
,`login_attempts` int(11)
,`locked_until` timestamp
,`last_login` timestamp
);

-- --------------------------------------------------------

--
-- Struktur untuk view `booking_summary_view`
--
DROP TABLE IF EXISTS `booking_summary_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `booking_summary_view`  AS SELECT `b`.`id` AS `id`, `b`.`nama` AS `nama`, `b`.`no_hp` AS `no_hp`, `b`.`tipe_kamar` AS `tipe_kamar`, `b`.`jumlah_tamu` AS `jumlah_tamu`, `b`.`jumlah_malam` AS `jumlah_malam`, `b`.`harga_total` AS `harga_total`, `b`.`status` AS `status`, `b`.`created_at` AS `created_at`, `u`.`nama_lengkap` AS `user_nama`, `u`.`email` AS `user_email` FROM (`booking` `b` join `users` `u` on(`b`.`user_id` = `u`.`id`)) ;

-- --------------------------------------------------------

--
-- Struktur untuk view `user_auth_view`
--
DROP TABLE IF EXISTS `user_auth_view`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_auth_view`  AS SELECT `u`.`id` AS `id`, `u`.`username` AS `username`, `u`.`password` AS `password`, `u`.`nama_lengkap` AS `nama_lengkap`, `u`.`email` AS `email`, `u`.`role_id` AS `role_id`, `r`.`nama_role` AS `nama_role`, `u`.`login_attempts` AS `login_attempts`, `u`.`locked_until` AS `locked_until`, `u`.`last_login` AS `last_login` FROM (`users` `u` join `roles` `r` on(`u`.`role_id` = `r`.`id`)) WHERE `u`.`role_id` in (1,2) ;

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `metode_pembayaran_id` (`metode_pembayaran_id`),
  ADD KEY `verified_by` (`verified_by`);

--
-- Indeks untuk tabel `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_kamar` (`nomor_kamar`);

--
-- Indeks untuk tabel `room_fasilitas`
--
ALTER TABLE `room_fasilitas`
  ADD PRIMARY KEY (`room_id`,`fasilitas_id`),
  ADD KEY `fasilitas_id` (`fasilitas_id`);

--
-- Indeks untuk tabel `security_sessions`
--
ALTER TABLE `security_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_expires_at` (`expires_at`),
  ADD KEY `idx_is_active` (`is_active`);

--
-- Indeks untuk tabel `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `testimoni`
--
ALTER TABLE `testimoni`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `idx_password_reset_token` (`password_reset_token`),
  ADD KEY `idx_locked_until` (`locked_until`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `fasilitas`
--
ALTER TABLE `fasilitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=94;

--
-- AUTO_INCREMENT untuk tabel `metode_pembayaran`
--
ALTER TABLE `metode_pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `security_sessions`
--
ALTER TABLE `security_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `testimoni`
--
ALTER TABLE `testimoni`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `booking_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `log_aktivitas_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `notifikasi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`),
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`metode_pembayaran_id`) REFERENCES `metode_pembayaran` (`id`),
  ADD CONSTRAINT `pembayaran_ibfk_3` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `room_fasilitas`
--
ALTER TABLE `room_fasilitas`
  ADD CONSTRAINT `room_fasilitas_ibfk_1` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`),
  ADD CONSTRAINT `room_fasilitas_ibfk_2` FOREIGN KEY (`fasilitas_id`) REFERENCES `fasilitas` (`id`);

--
-- Ketidakleluasaan untuk tabel `security_sessions`
--
ALTER TABLE `security_sessions`
  ADD CONSTRAINT `security_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `testimoni`
--
ALTER TABLE `testimoni`
  ADD CONSTRAINT `testimoni_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `testimoni_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`),
  ADD CONSTRAINT `testimoni_ibfk_3` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`);

--
-- Ketidakleluasaan untuk tabel `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
