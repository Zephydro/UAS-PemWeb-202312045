<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - HotelEase</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <!-- Hamburger panel will be injected by hamburger-universal.js -->
    <?php require('sidebar.php'); ?>

<div class="sidebar-overlay"></div>
<button class="hamburger-btn">
    <div class="hamburger-icon">
        <span></span>
        <span></span>
        <span></span>
    </div>
</button>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold text-gold mb-2 fade-in-up">Dashboard Admin</h2>
                <p class="text-muted fade-in-up">Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Admin') ?></p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-people-fill text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Total Pengguna</h6>
                        <h3 class="text-gold fw-bold">128</h3>
                        <small class="text-success">
                            <i class="bi bi-arrow-up"></i> +12% dari bulan lalu
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-door-closed-fill text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Jumlah Kamar</h6>
                        <h3 class="text-gold fw-bold">35</h3>
                        <small class="text-info">
                            <i class="bi bi-info-circle"></i> 28 tersedia
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-calendar-check-fill text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Booking Aktif</h6>
                        <h3 class="text-gold fw-bold">22</h3>
                        <small class="text-warning">
                            <i class="bi bi-clock"></i> 5 check-in hari ini
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-cash-coin text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Total Pendapatan</h6>
                        <h3 class="text-gold fw-bold">Rp 9.300.000</h3>
                        <small class="text-success">
                            <i class="bi bi-arrow-up"></i> +8% dari target
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card fade-in-up">
                    <div class="card-header">
                        <h5 class="fw-bold text-gold mb-0">
                            <i class="bi bi-graph-up me-2"></i>Pendapatan Bulanan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Progress Target Bulanan</span>
                                <span class="fw-bold text-gold">70%</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-gradient" 
                                     style="background: linear-gradient(135deg, var(--primary-gold), #B8860B); width: 70%;"
                                     role="progressbar">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted">Rp 9.300.000</small>
                                <small class="text-muted">Target: Rp 13.000.000</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row">
            <div class="col-12">
                <div class="card fade-in-up">
                    <div class="card-header">
                        <h5 class="fw-bold text-gold mb-0">
                            <i class="bi bi-lightning-fill me-2"></i>Aksi Cepat
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <a href="kamar_tambah.php" class="btn btn-primary w-100">
                                    <i class="bi bi-plus-circle me-2"></i>Tambah Kamar
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="users.php" class="btn btn-outline-dark w-100">
                                    <i class="bi bi-people me-2"></i>Kelola User
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="booking_admin.php" class="btn btn-outline-dark w-100">
                                    <i class="bi bi-calendar-check me-2"></i>Lihat Booking
                                </a>
                            </div>
                            <div class="col-md-3">
                                <a href="reports.php" class="btn btn-outline-dark w-100">
                                    <i class="bi bi-file-earmark-text me-2"></i>Laporan
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/admin.js"></script>

</body>
</html>
