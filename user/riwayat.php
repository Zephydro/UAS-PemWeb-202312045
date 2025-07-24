<?php
session_start();
require_once '../config/koneksi.php';
require_once '../config/helpers.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil riwayat booking user
$query = "SELECT b.*, b.tipe_kamar as nama_kamar, b.harga_total as total_harga, p.status as status_pembayaran, mp.nama_metode as metode_pembayaran, p.tanggal_pembayaran 
          FROM booking b 
          LEFT JOIN pembayaran p ON b.id = p.booking_id 
          LEFT JOIN metode_pembayaran mp ON p.metode_pembayaran_id = mp.id 
          WHERE b.user_id = ? 
          ORDER BY b.created_at DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Riwayat Booking - HotelEase</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <!-- Hamburger panel will be injected by hamburger-universal.js -->
    <?php require('sidebar.php'); ?>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold text-gold mb-2 fade-in-up">Riwayat Booking</h2>
                <p class="text-muted fade-in-up">Lihat semua riwayat pemesanan kamar Anda</p>
            </div>
        </div>

        <?php if ($bookings->num_rows > 0): ?>
            <div class="row g-4">
                <?php while ($booking = $bookings->fetch_assoc()): ?>
                    <div class="col-12">
                        <div class="card shadow-lg fade-in-up booking-card" onclick="location.href='transaksi.php?booking_id=<?= $booking['id'] ?>'" style="cursor: pointer; transition: transform 0.2s;">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-lg-8">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="booking-icon me-3">
                                                <i class="bi bi-calendar-check text-gold"></i>
                                            </div>
                                            <div>
                                                <h5 class="mb-1 text-gold"><?= htmlspecialchars($booking['nama_kamar']) ?></h5>
                                                <small class="text-muted">Booking ID: #<?= $booking['id'] ?></small>
                                            </div>
                                        </div>
                                        
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <div class="info-item">
                                                    <i class="bi bi-people me-2 text-primary"></i>
                                                    <span class="fw-semibold">Jumlah Tamu:</span> <?= $booking['jumlah_tamu'] ?> orang
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-item">
                                                    <i class="bi bi-moon me-2 text-primary"></i>
                                                    <span class="fw-semibold">Jumlah Malam:</span> <?= $booking['jumlah_malam'] ?> malam
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-item">
                                                    <i class="bi bi-currency-dollar me-2 text-success"></i>
                                                    <span class="fw-semibold">Total Harga:</span> <?= formatCurrency($booking['total_harga']) ?>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="info-item">
                                                    <i class="bi bi-calendar me-2 text-info"></i>
                                                    <span class="fw-semibold">Tanggal Booking:</span> <?= formatDate($booking['created_at']) ?>
                                                </div>
                                            </div>
                                            <?php if ($booking['catatan']): ?>
                                            <div class="col-12">
                                                <div class="info-item">
                                                    <i class="bi bi-chat-text me-2 text-warning"></i>
                                                    <span class="fw-semibold">Catatan:</span> <?= htmlspecialchars($booking['catatan']) ?>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-lg-4">
                                        <div class="text-lg-end">
                                            <div class="mb-3">
                                                <span class="fw-semibold d-block mb-2">Status Booking:</span>
                                                <?= getStatusBadge($booking['status']) ?>
                                            </div>
                                            
                                            <?php if ($booking['status_pembayaran']): ?>
                                                <div class="mb-3">
                                                    <span class="fw-semibold d-block mb-2">Status Pembayaran:</span>
                                                    <?= getStatusBadge($booking['status_pembayaran']) ?>
                                                </div>
                                                
                                                <?php if ($booking['metode_pembayaran']): ?>
                                                    <div class="mb-2">
                                                        <small class="text-muted">
                                                            <i class="bi bi-credit-card me-1"></i>
                                                            <?= htmlspecialchars($booking['metode_pembayaran']) ?>
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <?php if ($booking['tanggal_pembayaran']): ?>
                                                    <div class="mb-3">
                                                        <small class="text-muted">
                                                            <i class="bi bi-calendar-check me-1"></i>
                                                            Dibayar: <?= formatDate($booking['tanggal_pembayaran']) ?>
                                                        </small>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            
                                            <?php if ($booking['status'] == 'selesai'): ?>
                                                <?php
                                                // Cek apakah sudah ada testimoni
                                                $check_testimoni = $conn->prepare("SELECT id FROM testimoni WHERE booking_id = ?");
                                                $check_testimoni->bind_param("i", $booking['id']);
                                                $check_testimoni->execute();
                                                $testimoni_exists = $check_testimoni->get_result()->num_rows > 0;
                                                ?>
                                                <?php if (!$testimoni_exists): ?>
                                                    <a href="testimoni.php?booking_id=<?= $booking['id'] ?>" class="btn btn-primary" onclick="event.stopPropagation();">
                                                        <i class="bi bi-chat-quote me-1"></i>Beri Testimoni
                                                    </a>
                                                <?php else: ?>
                                                    <span class="badge bg-success fs-6">
                                                        <i class="bi bi-check-circle me-1"></i>Testimoni Diberikan
                                                    </span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5 fade-in-up">
                <div class="empty-state">
                    <i class="bi bi-calendar-x display-1 text-muted mb-4"></i>
                    <h3 class="text-gold mb-3">Belum Ada Riwayat Booking</h3>
                    <p class="text-muted mb-4">Anda belum pernah melakukan booking. Mulai booking sekarang dan nikmati pengalaman menginap yang tak terlupakan!</p>
                    <a href="dashboard.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-calendar-plus me-2"></i>Booking Sekarang
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Include JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/user.js"></script>

<style>
.booking-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
}

.booking-card {
    border: none;
    border-radius: 15px;
    overflow: hidden;
}

.booking-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.info-item {
    padding: 8px 0;
    border-bottom: 1px solid #f0f0f0;
}

.info-item:last-child {
    border-bottom: none;
}

.booking-card::after {
    content: "Klik untuk detail";
    position: absolute;
    top: 10px;
    right: 15px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 0.75rem;
    opacity: 0;
    transition: opacity 0.3s;
}

.booking-card:hover::after {
    opacity: 1;
}

.booking-card {
    position: relative;
}
</style>

</body>
</html>