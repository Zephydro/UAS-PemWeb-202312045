<?php
include '../config/koneksi.php';
session_start();

// Cek akses admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Handle tambah booking
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $tipe_kamar = $_POST['tipe_kamar'];
    $jumlah_tamu = $_POST['jumlah_tamu'];
    $jumlah_malam = $_POST['jumlah_malam'];
    $catatan = $_POST['catatan'];
    $harga_total = $_POST['harga_total'];

    mysqli_query($conn, "INSERT INTO booking 
        (nama, no_hp, tipe_kamar, jumlah_tamu, jumlah_malam, catatan, harga_total, status, created_at) VALUES 
        ('$nama', '$no_hp', '$tipe_kamar', $jumlah_tamu, $jumlah_malam, '$catatan', $harga_total, 'diajukan', NOW())");
}

// Handle edit booking
if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['nama'];
    $no_hp = $_POST['no_hp'];
    $tipe_kamar = $_POST['tipe_kamar'];
    $jumlah_tamu = $_POST['jumlah_tamu'];
    $jumlah_malam = $_POST['jumlah_malam'];
    $catatan = $_POST['catatan'];
    $harga_total = $_POST['harga_total'];
    $status = $_POST['status'];

    mysqli_query($conn, "UPDATE booking SET 
        nama='$nama', no_hp='$no_hp', tipe_kamar='$tipe_kamar',
        jumlah_tamu=$jumlah_tamu, jumlah_malam=$jumlah_malam,
        catatan='$catatan', harga_total=$harga_total, status='$status'
        WHERE id=$id");
}

// Handle hapus
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM booking WHERE id=$id");
}

// Ambil semua data
$data = mysqli_query($conn, "SELECT * FROM booking ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Booking - HotelEase</title>
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
                <h2 class="fw-bold text-gold mb-2 fade-in-up">Manajemen Booking</h2>
                <p class="text-muted fade-in-up">Kelola semua reservasi hotel</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <?php
            $stats_query = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'diajukan' THEN 1 ELSE 0 END) as diajukan,
                SUM(CASE WHEN status = 'diterima' THEN 1 ELSE 0 END) as diterima,
                SUM(CASE WHEN status = 'ditolak' THEN 1 ELSE 0 END) as ditolak
                FROM booking";
            $stats_result = mysqli_query($conn, $stats_query);
            $stats = mysqli_fetch_assoc($stats_result);
            ?>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-calendar-check-fill text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Total Booking</h6>
                        <h3 class="text-gold fw-bold"><?= $stats['total'] ?></h3>
                        <small class="text-info">
                            <i class="bi bi-info-circle"></i> Semua reservasi
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-clock-fill text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Menunggu</h6>
                        <h3 class="text-gold fw-bold"><?= $stats['diajukan'] ?></h3>
                        <small class="text-warning">
                            <i class="bi bi-hourglass-split"></i> Perlu review
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
                        <h6 class="text-muted mb-2">Diterima</h6>
                        <h3 class="text-gold fw-bold"><?= $stats['diterima'] ?></h3>
                        <small class="text-success">
                            <i class="bi bi-check-circle"></i> Dikonfirmasi
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-x-circle-fill text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Ditolak</h6>
                        <h3 class="text-gold fw-bold"><?= $stats['ditolak'] ?></h3>
                        <small class="text-danger">
                            <i class="bi bi-x-circle"></i> Tidak disetujui
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Table -->
        <div class="card fade-in-up">
            <div class="card-header">
                <h5 class="fw-bold text-gold mb-0">
                    <i class="bi bi-list-check me-2"></i>Daftar Booking
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-gold">Tamu</th>
                                <th class="text-gold">Kamar</th>
                                <th class="text-gold">Detail</th>
                                <th class="text-gold">Harga</th>
                                <th class="text-gold">Status</th>
                                <th class="text-gold">Catatan</th>
                                <th class="text-gold">Waktu</th>
                                <th class="text-gold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while($row = mysqli_fetch_assoc($data)): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3">
                                            <?= strtoupper(substr($row['nama'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-bold"><?= htmlspecialchars($row['nama']) ?></div>
                                            <small class="text-muted">
                                                <i class="bi bi-telephone me-1"></i><?= htmlspecialchars($row['no_hp']) ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold"><?= htmlspecialchars($row['tipe_kamar']) ?></span>
                                </td>
                                <td>
                                    <div class="small">
                                        <div><i class="bi bi-people me-1 text-muted"></i><?= $row['jumlah_tamu'] ?> tamu</div>
                                        <div><i class="bi bi-moon me-1 text-muted"></i><?= $row['jumlah_malam'] ?> malam</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-gold fw-bold">
                                        Rp <?= number_format($row['harga_total'], 0, ',', '.') ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $badge_class = '';
                                    $icon = '';
                                    switch($row['status']) {
                                        case 'diterima': 
                                            $badge_class = 'bg-success'; 
                                            $icon = 'bi-check-circle';
                                            break;
                                        case 'ditolak': 
                                            $badge_class = 'bg-danger'; 
                                            $icon = 'bi-x-circle';
                                            break;
                                        case 'diverifikasi': 
                                            $badge_class = 'bg-info'; 
                                            $icon = 'bi-shield-check';
                                            break;
                                        default: 
                                            $badge_class = 'bg-secondary'; 
                                            $icon = 'bi-clock';
                                            break;
                                    }
                                    ?>
                                    <span class="badge <?= $badge_class ?>">
                                        <i class="bi <?= $icon ?> me-1"></i>
                                        <?= ucfirst($row['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-muted small"><?= htmlspecialchars($row['catatan']) ?></span>
                                </td>
                                <td>
                                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" 
                                                data-bs-target="#editModal<?= $row['id'] ?>" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Hapus booking ini?')" 
                                           class="btn btn-sm btn-outline-danger" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal Edit -->
                            <div class="modal fade" id="editModal<?= $row['id'] ?>" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <form method="post">
                                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                            <div class="modal-header">
                                                <h5 class="modal-title text-gold fw-bold">
                                                    <i class="bi bi-pencil-square me-2"></i>Edit Booking
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Nama Tamu</label>
                                                        <input type="text" name="nama" value="<?= htmlspecialchars($row['nama']) ?>" 
                                                               class="form-control" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">No. HP</label>
                                                        <input type="text" name="no_hp" value="<?= htmlspecialchars($row['no_hp']) ?>" 
                                                               class="form-control" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Tipe Kamar</label>
                                                        <input type="text" name="tipe_kamar" value="<?= htmlspecialchars($row['tipe_kamar']) ?>" 
                                                               class="form-control" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Jumlah Tamu</label>
                                                        <input type="number" name="jumlah_tamu" value="<?= $row['jumlah_tamu'] ?>" 
                                                               class="form-control" required>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label">Jumlah Malam</label>
                                                        <input type="number" name="jumlah_malam" value="<?= $row['jumlah_malam'] ?>" 
                                                               class="form-control" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Harga Total</label>
                                                        <input type="number" name="harga_total" value="<?= $row['harga_total'] ?>" 
                                                               class="form-control" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="diajukan" <?= $row['status']=='diajukan'?'selected':'' ?>>Diajukan</option>
                                                            <option value="diverifikasi" <?= $row['status']=='diverifikasi'?'selected':'' ?>>Diverifikasi</option>
                                                            <option value="diterima" <?= $row['status']=='diterima'?'selected':'' ?>>Diterima</option>
                                                            <option value="ditolak" <?= $row['status']=='ditolak'?'selected':'' ?>>Ditolak</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label">Catatan</label>
                                                        <textarea name="catatan" class="form-control" rows="3"><?= htmlspecialchars($row['catatan']) ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                    <i class="bi bi-x-lg me-1"></i>Batal
                                                </button>
                                                <button type="submit" name="edit" class="btn btn-primary">
                                                    <i class="bi bi-check-lg me-1"></i>Simpan Perubahan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
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
