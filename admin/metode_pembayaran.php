<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Handle Add Metode Pembayaran
if (isset($_POST['add_metode'])) {
    $nama_metode = $_POST['nama_metode'];
    $deskripsi = $_POST['deskripsi'];
    $nomor_rekening = $_POST['nomor_rekening'];
    $atas_nama = $_POST['atas_nama'];
    
    $stmt = $conn->prepare("INSERT INTO metode_pembayaran (nama_metode, deskripsi, nomor_rekening, atas_nama) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama_metode, $deskripsi, $nomor_rekening, $atas_nama);
    $stmt->execute();
    header("Location: metode_pembayaran.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM metode_pembayaran WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: metode_pembayaran.php");
    exit;
}

// Handle Status Toggle
if (isset($_GET['toggle'])) {
    $id = $_GET['toggle'];
    $stmt = $conn->prepare("UPDATE metode_pembayaran SET status = CASE WHEN status = 'aktif' THEN 'nonaktif' ELSE 'aktif' END WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: metode_pembayaran.php");
    exit;
}

$query = "SELECT * FROM metode_pembayaran ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metode Pembayaran - HotelEase</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="admin-body">
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

    <div class="main-content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
                <div>
                    <h2 class="fw-bold text-gold mb-1">
                        <i class="bi bi-credit-card-2-front me-2"></i>Metode Pembayaran
                    </h2>
                    <p class="text-muted mb-0">Kelola metode pembayaran yang tersedia</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Metode
                </button>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4 fade-in-up">
                <?php
                $total_metode = mysqli_num_rows($result);
                mysqli_data_seek($result, 0);
                $aktif_count = 0;
                $nonaktif_count = 0;
                while ($row = mysqli_fetch_assoc($result)) {
                    if ($row['status'] == 'aktif') $aktif_count++;
                    else $nonaktif_count++;
                }
                mysqli_data_seek($result, 0);
                ?>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                    <i class="bi bi-credit-card text-primary fs-4"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold text-primary mb-1"><?= $total_metode ?></h3>
                            <p class="text-muted mb-0">Total Metode</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                    <i class="bi bi-check-circle text-success fs-4"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold text-success mb-1"><?= $aktif_count ?></h3>
                            <p class="text-muted mb-0">Metode Aktif</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <div class="rounded-circle bg-secondary bg-opacity-10 p-3">
                                    <i class="bi bi-x-circle text-secondary fs-4"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold text-secondary mb-1"><?= $nonaktif_count ?></h3>
                            <p class="text-muted mb-0">Metode Nonaktif</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Metode Pembayaran Table -->
            <div class="card border-0 shadow-sm fade-in-up">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0 text-gold">
                        <i class="bi bi-list-ul me-2"></i>Daftar Metode Pembayaran
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="border-0 ps-4">ID</th>
                                    <th class="border-0">Nama Metode</th>
                                    <th class="border-0">Deskripsi</th>
                                    <th class="border-0">Nomor Rekening</th>
                                    <th class="border-0">Atas Nama</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0 pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="border-bottom">
                                    <td class="ps-4 fw-bold"><?= $row['id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-credit-card me-2 text-primary"></i>
                                            <span class="fw-semibold"><?= htmlspecialchars($row['nama_metode']) ?></span>
                                        </div>
                                    </td>
                                    <td class="text-muted"><?= htmlspecialchars($row['deskripsi']) ?: '-' ?></td>
                                    <td class="font-monospace"><?= htmlspecialchars($row['nomor_rekening']) ?: '-' ?></td>
                                    <td><?= htmlspecialchars($row['atas_nama']) ?: '-' ?></td>
                                    <td>
                                        <span class="badge <?= $row['status'] == 'aktif' ? 'bg-success' : 'bg-secondary' ?> px-3 py-2">
                                            <i class="bi bi-<?= $row['status'] == 'aktif' ? 'check-circle' : 'x-circle' ?> me-1"></i>
                                            <?= ucfirst($row['status']) ?>
                                        </span>
                                    </td>
                                    <td class="pe-4">
                                        <div class="btn-group" role="group">
                                            <a href="?toggle=<?= $row['id'] ?>" 
                                               class="btn btn-sm btn-outline-warning" 
                                               onclick="return confirm('Ubah status metode pembayaran?')"
                                               title="Toggle Status">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </a>
                                            <a href="?delete=<?= $row['id'] ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('Hapus metode pembayaran ini?')"
                                               title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-credit-card text-muted" style="font-size: 4rem;"></i>
                        <h5 class="text-muted mt-3">Belum ada metode pembayaran</h5>
                        <p class="text-muted">Tambahkan metode pembayaran pertama Anda</p>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Metode
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Metode Pembayaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nama Metode</label>
                                <input type="text" class="form-control" name="nama_metode" required 
                                       placeholder="Contoh: Bank BCA, GoPay, OVO">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nomor Rekening/Akun</label>
                                <input type="text" class="form-control" name="nomor_rekening" 
                                       placeholder="Nomor rekening atau ID akun">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Atas Nama</label>
                                <input type="text" class="form-control" name="atas_nama" 
                                       placeholder="Nama pemilik rekening/akun">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Deskripsi</label>
                                <textarea class="form-control" name="deskripsi" rows="3" 
                                          placeholder="Deskripsi atau instruksi pembayaran..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </button>
                        <button type="submit" name="add_metode" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>Simpan Metode
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/admin.js"></script>

</body>
</html>