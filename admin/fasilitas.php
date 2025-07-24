<?php
session_start();
require '../config/koneksi.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $nama_fasilitas = mysqli_real_escape_string($conn, $_POST['nama']);
                $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
                $harga_tambahan = (float)$_POST['harga'];
                
                $query = "INSERT INTO fasilitas (nama_fasilitas, deskripsi, harga_tambahan, status) VALUES ('$nama_fasilitas', '$deskripsi', $harga_tambahan, 'aktif')";
                if (mysqli_query($conn, $query)) {
                    $success = "Fasilitas berhasil ditambahkan!";
                } else {
                    $error = "Gagal menambahkan fasilitas: " . mysqli_error($conn);
                }
                break;
                
            case 'edit':
                $id = (int)$_POST['id'];
                $nama_fasilitas = mysqli_real_escape_string($conn, $_POST['nama']);
                $deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
                $harga_tambahan = (float)$_POST['harga'];
                $status = mysqli_real_escape_string($conn, $_POST['status']);
                
                $query = "UPDATE fasilitas SET nama_fasilitas='$nama_fasilitas', deskripsi='$deskripsi', harga_tambahan=$harga_tambahan, status='$status' WHERE id=$id";
                if (mysqli_query($conn, $query)) {
                    $success = "Fasilitas berhasil diperbarui!";
                } else {
                    $error = "Gagal memperbarui fasilitas: " . mysqli_error($conn);
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['id'];
                $query = "DELETE FROM fasilitas WHERE id=$id";
                if (mysqli_query($conn, $query)) {
                    $success = "Fasilitas berhasil dihapus!";
                } else {
                    $error = "Gagal menghapus fasilitas: " . mysqli_error($conn);
                }
                break;
                
            case 'toggle_status':
                $id = (int)$_POST['id'];
                $status = $_POST['status'] == 'aktif' ? 'nonaktif' : 'aktif';
                $query = "UPDATE fasilitas SET status='$status' WHERE id=$id";
                if (mysqli_query($conn, $query)) {
                    $success = "Status fasilitas berhasil diubah!";
                } else {
                    $error = "Gagal mengubah status fasilitas: " . mysqli_error($conn);
                }
                break;
        }
    }
}

// Ambil data fasilitas
$query = "SELECT * FROM fasilitas ORDER BY nama_fasilitas";
$result = mysqli_query($conn, $query);
$fasilitas = [];
while ($row = mysqli_fetch_assoc($result)) {
    $fasilitas[] = $row;
}

// Statistik
$total_fasilitas = count($fasilitas);
$aktif = count(array_filter($fasilitas, function($f) { return $f['status'] == 'aktif'; }));
$nonaktif = $total_fasilitas - $aktif;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen Fasilitas - HotelEase</title>
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
                <h2 class="fw-bold text-gold mb-2 fade-in-up">Manajemen Fasilitas</h2>
                <p class="text-muted fade-in-up">Kelola fasilitas hotel dan layanan tambahan</p>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary fade-in-up" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Fasilitas
                </button>
            </div>
        </div>

        <?php if (isset($success)): ?>
        <div class="alert alert-success alert-dismissible fade show fade-in-up" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show fade-in-up" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <!-- Statistics -->
        <div class="row mb-4 g-4">
            <div class="col-md-4">
                <div class="card shadow-lg border-0 fade-in-up">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-3">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                <i class="bi bi-stars fs-1 text-primary"></i>
                            </div>
                        </div>
                        <h5 class="text-muted mb-1">Total Fasilitas</h5>
                        <h2 class="fw-bold text-primary"><?= $total_fasilitas ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-lg border-0 fade-in-up">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-3">
                            <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                <i class="bi bi-check-circle fs-1 text-success"></i>
                            </div>
                        </div>
                        <h5 class="text-muted mb-1">Aktif</h5>
                        <h2 class="fw-bold text-success"><?= $aktif ?></h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-lg border-0 fade-in-up">
                    <div class="card-body text-center">
                        <div class="d-flex align-items-center justify-content-center mb-3">
                            <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                <i class="bi bi-pause-circle fs-1 text-warning"></i>
                            </div>
                        </div>
                        <h5 class="text-muted mb-1">Nonaktif</h5>
                        <h2 class="fw-bold text-warning"><?= $nonaktif ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fasilitas Table -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Daftar Fasilitas</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Fasilitas</th>
                                <th>Deskripsi</th>
                                <th>Harga Tambahan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($fasilitas as $f): ?>
                            <tr>
                                <td><?= $f['id'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($f['nama_fasilitas']) ?></strong>
                                </td>
                                <td>
                                    <small class="text-muted"><?= nl2br(htmlspecialchars($f['deskripsi'])) ?></small>
                                </td>
                                <td>Rp <?= number_format($f['harga_tambahan'], 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge <?= $f['status'] == 'aktif' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= ucfirst($f['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-primary" onclick="editFasilitas(<?= htmlspecialchars(json_encode($f)) ?>)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin mengubah status?')">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="id" value="<?= $f['id'] ?>">
                                            <input type="hidden" name="status" value="<?= $f['status'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-warning">
                                                <i class="bi bi-toggle-<?= $f['status'] == 'aktif' ? 'on' : 'off' ?>"></i>
                                            </button>
                                        </form>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Yakin ingin menghapus fasilitas ini?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?= $f['id'] ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Fasilitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="mb-3">
                        <label class="form-label">Nama Fasilitas</label>
                        <input type="text" class="form-control" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" class="form-control" name="harga" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select class="form-control" name="kategori" required>
                            <option value="">Pilih Kategori</option>
                            <option value="vip">VIP</option>
                            <option value="olahraga">Olahraga</option>
                            <option value="hiburan">Hiburan</option>
                            <option value="makanan">Makanan & Minuman</option>
                            <option value="transportasi">Transportasi</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Fasilitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">Nama Fasilitas</label>
                        <input type="text" class="form-control" name="nama" id="edit_nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" id="edit_deskripsi" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input type="number" class="form-control" name="harga" id="edit_harga" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori</label>
                        <select class="form-control" name="kategori" id="edit_kategori" required>
                            <option value="vip">VIP</option>
                            <option value="olahraga">Olahraga</option>
                            <option value="hiburan">Hiburan</option>
                            <option value="makanan">Makanan & Minuman</option>
                            <option value="transportasi">Transportasi</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" name="status" id="edit_status" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/admin.js"></script>
<script>
function editFasilitas(fasilitas) {
    document.getElementById('edit_id').value = fasilitas.id;
    document.getElementById('edit_nama').value = fasilitas.nama_fasilitas;
    document.getElementById('edit_deskripsi').value = fasilitas.deskripsi;
    document.getElementById('edit_harga').value = fasilitas.harga_tambahan;
    document.getElementById('edit_status').value = fasilitas.status;
    
    var myModal = new bootstrap.Modal(document.getElementById('editModal'), {});
    myModal.show();
}
</script>
</body>
</html>