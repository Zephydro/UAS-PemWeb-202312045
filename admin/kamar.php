<?php
session_start();
include '../config/koneksi.php';
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil data kamar
$query = "SELECT * FROM rooms ORDER BY nomor_kamar";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Kamar - HotelEase</title>
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
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold text-gold mb-2 fade-in-up">Manajemen Kamar</h2>
                        <p class="text-muted fade-in-up">Kelola semua kamar hotel</p>
                    </div>
                    <a href="kamar_tambah.php" class="btn btn-primary fade-in-up">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Kamar
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <?php
            $stats_query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'tersedia' THEN 1 ELSE 0 END) as tersedia,
                SUM(CASE WHEN status = 'terisi' THEN 1 ELSE 0 END) as terisi,
                SUM(CASE WHEN status = 'maintenance' THEN 1 ELSE 0 END) as maintenance
                FROM rooms";
            $stats_result = mysqli_query($conn, $stats_query);
            $stats = mysqli_fetch_assoc($stats_result);
            ?>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-house-door-fill text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Total Kamar</h6>
                        <h3 class="text-gold fw-bold"><?= $stats['total'] ?></h3>
                        <small class="text-info">
                            <i class="bi bi-info-circle"></i> Semua kamar hotel
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-check-circle-fill text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Tersedia</h6>
                        <h3 class="text-gold fw-bold"><?= $stats['tersedia'] ?></h3>
                        <small class="text-success">
                            <i class="bi bi-check-circle"></i> Siap dipesan
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-person-fill-check text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Terisi</h6>
                        <h3 class="text-gold fw-bold"><?= $stats['terisi'] ?></h3>
                        <small class="text-warning">
                            <i class="bi bi-clock"></i> Sedang ditempati
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-tools text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Maintenance</h6>
                        <h3 class="text-gold fw-bold"><?= $stats['maintenance'] ?></h3>
                        <small class="text-danger">
                            <i class="bi bi-exclamation-triangle"></i> Dalam perbaikan
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rooms Table -->
        <div class="card fade-in-up">
            <div class="card-header">
                <h5 class="fw-bold text-gold mb-0">
                    <i class="bi bi-grid-3x3-gap me-2"></i>Daftar Kamar
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-gold">ID</th>
                                <th class="text-gold">Foto</th>
                                <th class="text-gold">Nomor Kamar</th>
                                <th class="text-gold">Tipe Kamar</th>
                                <th class="text-gold">Harga/Malam</th>
                                <th class="text-gold">Kapasitas</th>
                                <th class="text-gold">Status</th>
                                <th class="text-gold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><span class="badge bg-light text-dark"><?= $row['id'] ?></span></td>
                                <td>
                                    <?php if ($row['foto']): ?>
                                        <img src="../uploads/<?= htmlspecialchars($row['foto']) ?>" 
                                             alt="Foto Kamar" class="img-thumbnail rounded-3" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded-3" 
                                             style="width: 60px; height: 60px;">
                                            <i class="bi bi-image text-muted fs-4"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="room-number-badge me-2">
                                            <?= htmlspecialchars($row['nomor_kamar']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold"><?= htmlspecialchars($row['tipe_kamar']) ?></span>
                                </td>
                                <td>
                                    <span class="text-gold fw-bold">
                                        Rp <?= number_format($row['harga_per_malam'], 0, ',', '.') ?>
                                    </span>
                                </td>
                                <td>
                                    <i class="bi bi-people me-1 text-muted"></i>
                                    <?= $row['kapasitas'] ?? 2 ?> orang
                                </td>
                                <td>
                                    <?php
                                    $badge_class = '';
                                    $icon = '';
                                    switch($row['status']) {
                                        case 'tersedia': 
                                            $badge_class = 'bg-success'; 
                                            $icon = 'bi-check-circle';
                                            break;
                                        case 'terisi': 
                                            $badge_class = 'bg-warning'; 
                                            $icon = 'bi-person-fill';
                                            break;
                                        case 'maintenance': 
                                            $badge_class = 'bg-danger'; 
                                            $icon = 'bi-tools';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?= $badge_class ?>">
                                        <i class="bi <?= $icon ?> me-1"></i>
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="kamar_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="kamar_hapus.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Yakin ingin menghapus kamar ini?')" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
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
