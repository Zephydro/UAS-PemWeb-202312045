<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Handle Clear Logs
if (isset($_POST['clear_logs'])) {
    $days = $_POST['days'];
    $stmt = $conn->prepare("DELETE FROM log_aktivitas WHERE tanggal < DATE_SUB(NOW(), INTERVAL ? DAY)");
    $stmt->bind_param("i", $days);
    $stmt->execute();
    header("Location: log_aktivitas.php");
    exit;
}

// Filter parameters
$filter_user = isset($_GET['user']) ? $_GET['user'] : '';
$filter_aktivitas = isset($_GET['aktivitas']) ? $_GET['aktivitas'] : '';
$filter_date = isset($_GET['date']) ? $_GET['date'] : '';

// Build query with filters
$where_conditions = [];
$params = [];
$param_types = '';

if ($filter_user) {
    $where_conditions[] = "l.user_id = ?";
    $params[] = $filter_user;
    $param_types .= 'i';
}

if ($filter_aktivitas) {
    $where_conditions[] = "l.aktivitas LIKE ?";
    $params[] = "%$filter_aktivitas%";
    $param_types .= 's';
}

if ($filter_date) {
    $where_conditions[] = "DATE(l.tanggal) = ?";
    $params[] = $filter_date;
    $param_types .= 's';
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

$query = "SELECT l.*, u.nama_lengkap, u.username 
          FROM log_aktivitas l 
          LEFT JOIN users u ON l.user_id = u.id 
          $where_clause
          ORDER BY l.tanggal DESC 
          LIMIT 100";

if (!empty($params)) {
    $stmt = $conn->prepare($query);
    $stmt->bind_param($param_types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = mysqli_query($conn, $query);
}

// Get users for filter
$users_query = "SELECT id, nama_lengkap FROM users ORDER BY nama_lengkap";
$users_result = mysqli_query($conn, $users_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="page-type" content="admin">
    <meta name="admin-name" content="<?= htmlspecialchars($_SESSION['nama_lengkap'] ?? 'Admin') ?>">
    <title>Log Aktivitas - HotelEase Admin</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="admin-page">
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
            <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
                <h2 class="mb-0 text-gold">
                    <i class="bi bi-activity me-2"></i>Log Aktivitas
                </h2>
                <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#clearModal">
                    <i class="bi bi-trash me-1"></i>Bersihkan Log
                </button>
            </div>

            <!-- Filters -->
            <div class="card shadow-lg mb-4 fade-in-up">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-funnel me-2"></i>Filter Log
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-person me-1"></i>User
                            </label>
                            <select class="form-select" name="user">
                                <option value="">Semua User</option>
                                <?php while ($user = mysqli_fetch_assoc($users_result)): ?>
                                    <option value="<?= $user['id'] ?>" <?= $filter_user == $user['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($user['nama_lengkap']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-search me-1"></i>Aktivitas
                            </label>
                            <input type="text" class="form-control" name="aktivitas" value="<?= htmlspecialchars($filter_aktivitas) ?>" placeholder="Cari aktivitas...">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-calendar me-1"></i>Tanggal
                            </label>
                            <input type="date" class="form-control" name="date" value="<?= htmlspecialchars($filter_date) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search me-1"></i>Filter
                                </button>
                                <a href="log_aktivitas.php" class="btn btn-secondary">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Log Table -->
            <div class="card shadow-lg fade-in-up">
                <div class="card-header bg-gradient-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-list-ul me-2"></i>Daftar Log Aktivitas
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th><i class="bi bi-hash me-1"></i>ID</th>
                                        <th><i class="bi bi-person me-1"></i>User</th>
                                        <th><i class="bi bi-activity me-1"></i>Aktivitas</th>
                                        <th><i class="bi bi-file-text me-1"></i>Deskripsi</th>
                                        <th><i class="bi bi-globe me-1"></i>IP Address</th>
                                        <th><i class="bi bi-clock me-1"></i>Tanggal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><span class="badge bg-light text-dark"><?= $row['id'] ?></span></td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary text-white me-2">
                                                    <?= strtoupper(substr($row['nama_lengkap'] ?? 'S', 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <strong><?= htmlspecialchars($row['nama_lengkap'] ?? 'System') ?></strong><br>
                                                    <small class="text-muted"><?= htmlspecialchars($row['username'] ?? '-') ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            $activity_colors = [
                                                'login' => 'bg-success',
                                                'logout' => 'bg-warning text-dark',
                                                'create' => 'bg-primary',
                                                'update' => 'bg-info',
                                                'delete' => 'bg-danger',
                                                'view' => 'bg-secondary'
                                            ];
                                            $activity = strtolower($row['aktivitas']);
                                            $badge_class = 'bg-primary';
                                            foreach ($activity_colors as $key => $color) {
                                                if (strpos($activity, $key) !== false) {
                                                    $badge_class = $color;
                                                    break;
                                                }
                                            }
                                            ?>
                                            <span class="badge <?= $badge_class ?>">
                                                <?= htmlspecialchars($row['aktivitas']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="description-preview" style="max-width: 300px;">
                                                <p class="mb-0 text-truncate" title="<?= htmlspecialchars($row['deskripsi'] ?? '-') ?>">
                                                    <?= htmlspecialchars($row['deskripsi'] ?? '-') ?>
                                                </p>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                <i class="bi bi-geo-alt me-1"></i>
                                                <?= htmlspecialchars($row['ip_address'] ?? '-') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar3 me-1"></i>
                                                <?= date('d/m/Y', strtotime($row['tanggal'])) ?><br>
                                                <i class="bi bi-clock me-1"></i>
                                                <?= date('H:i:s', strtotime($row['tanggal'])) ?>
                                            </small>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-activity display-1 text-muted mb-3"></i>
                            <h5 class="text-muted">Tidak ada log aktivitas</h5>
                            <p class="text-muted">Log aktivitas akan muncul di sini</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Clear Modal -->
<div class="modal fade" id="clearModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-gradient-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-trash me-2"></i>Bersihkan Log Aktivitas
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-x me-1"></i>Hapus log yang lebih lama dari:
                        </label>
                        <select class="form-select" name="days" required>
                            <option value="">Pilih periode</option>
                            <option value="7">7 hari</option>
                            <option value="30">30 hari</option>
                            <option value="90">90 hari</option>
                            <option value="365">1 tahun</option>
                        </select>
                    </div>
                    <div class="alert alert-warning d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                        <div>
                            <strong>Peringatan:</strong> Aksi ini tidak dapat dibatalkan! Log yang dihapus tidak dapat dikembalikan.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Batal
                    </button>
                    <button type="submit" name="clear_logs" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i>Hapus Log
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