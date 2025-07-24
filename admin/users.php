<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}
include '../config/koneksi.php';

// Ambil data users dengan role
$query = "SELECT u.*, r.nama_role FROM users u LEFT JOIN roles r ON u.role_id = r.id ORDER BY u.id";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Pengguna - HotelEase</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                        <h2 class="fw-bold text-gold mb-2 fade-in-up">Manajemen Pengguna</h2>
                        <p class="text-muted fade-in-up">Kelola semua pengguna sistem</p>
                    </div>
                    <a href="user_tambah.php" class="btn btn-primary fade-in-up">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Pengguna
                    </a>
                </div>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show fade-in-up" role="alert">
                <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show fade-in-up" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i><?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <?php
            $stats_query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN r.nama_role = 'admin' THEN 1 ELSE 0 END) as admin,
                SUM(CASE WHEN r.nama_role = 'user' THEN 1 ELSE 0 END) as user_count
                FROM users u LEFT JOIN roles r ON u.role_id = r.id";
            $stats_result = mysqli_query($conn, $stats_query);
            $stats = mysqli_fetch_assoc($stats_result);
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-people-fill text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Total Pengguna</h6>
                        <h3 class="text-gold fw-bold"><?= $stats['total'] ?></h3>
                        <small class="text-info">
                            <i class="bi bi-info-circle"></i> Semua pengguna terdaftar
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-shield-check text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Administrator</h6>
                        <h3 class="text-gold fw-bold"><?= $stats['admin'] ?></h3>
                        <small class="text-success">
                            <i class="bi bi-check-circle"></i> Akses penuh sistem
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-person-fill text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Pengguna Biasa</h6>
                        <h3 class="text-gold fw-bold"><?= $stats['user_count'] ?></h3>
                        <small class="text-primary">
                            <i class="bi bi-person-check"></i> Tamu hotel
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card fade-in-up">
            <div class="card-header">
                <h5 class="fw-bold text-gold mb-0">
                    <i class="bi bi-table me-2"></i>Daftar Pengguna
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-gold">ID</th>
                                <th class="text-gold">Nama Lengkap</th>
                                <th class="text-gold">Username</th>
                                <th class="text-gold">Email</th>
                                <th class="text-gold">Role</th>
                                <th class="text-gold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><span class="badge bg-light text-dark"><?= $row['id'] ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3">
                                            <?= strtoupper(substr($row['nama_lengkap'], 0, 1)) ?>
                                        </div>
                                        <strong><?= htmlspecialchars($row['nama_lengkap']) ?></strong>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($row['username']) ?></td>
                                <td>
                                    <i class="bi bi-envelope me-1 text-muted"></i>
                                    <?= htmlspecialchars($row['email']) ?>
                                </td>
                                <td>
                                    <?php
                                    $badge_class = $row['nama_role'] == 'admin' ? 'bg-danger' : 'bg-primary';
                                    $icon = $row['nama_role'] == 'admin' ? 'bi-shield-check' : 'bi-person';
                                    ?>
                                    <span class="badge <?= $badge_class ?>">
                                        <i class="bi <?= $icon ?> me-1"></i>
                                        <?= ucfirst($row['nama_role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="user_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                        <a href="user_hapus.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Yakin ingin menghapus pengguna ini?')" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                        <?php endif; ?>
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
