<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Handle Send Notification
if (isset($_POST['send_notification'])) {
    $user_id = $_POST['user_id'];
    $judul = $_POST['judul'];
    $pesan = $_POST['pesan'];
    $tipe = $_POST['tipe'];
    
    if ($user_id == 'all') {
        // Send to all users
        $users_query = "SELECT id FROM users WHERE role_id = 2"; // user
        $users_result = mysqli_query($conn, $users_query);
        
        while ($user = mysqli_fetch_assoc($users_result)) {
            $stmt = $conn->prepare("INSERT INTO notifikasi (user_id, judul, pesan, tipe) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("isss", $user['id'], $judul, $pesan, $tipe);
            $stmt->execute();
        }
    } else {
        $stmt = $conn->prepare("INSERT INTO notifikasi (user_id, judul, pesan, tipe) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $user_id, $judul, $pesan, $tipe);
        $stmt->execute();
    }
    
    header("Location: notifikasi.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM notifikasi WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: notifikasi.php");
    exit;
}

// Get all notifications
$query = "SELECT n.*, u.nama_lengkap 
          FROM notifikasi n 
          LEFT JOIN users u ON n.user_id = u.id 
          ORDER BY n.tanggal DESC";
$result = mysqli_query($conn, $query);

// Get all users for dropdown
$users_query = "SELECT id, nama_lengkap FROM users WHERE role_id = 2 ORDER BY nama_lengkap";
$users_result = mysqli_query($conn, $users_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Notifikasi - HotelEase</title>
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
                        <i class="bi bi-bell me-2"></i>Manajemen Notifikasi
                    </h2>
                    <p class="text-muted mb-0">Kelola dan kirim notifikasi kepada tamu</p>
                </div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-plus-circle me-1"></i>Kirim Notifikasi
                </button>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4 fade-in-up">
                <?php
                mysqli_data_seek($result, 0);
                $total_notifications = 0;
                $read_count = 0;
                $unread_count = 0;
                $type_counts = ['info' => 0, 'success' => 0, 'warning' => 0, 'error' => 0];
                
                while ($row = mysqli_fetch_assoc($result)) {
                    $total_notifications++;
                    if ($row['status'] == 'dibaca') $read_count++;
                    else $unread_count++;
                    if (isset($type_counts[$row['tipe']])) $type_counts[$row['tipe']]++;
                }
                mysqli_data_seek($result, 0);
                ?>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                    <i class="bi bi-bell text-primary fs-4"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold text-primary mb-1"><?= $total_notifications ?></h3>
                            <p class="text-muted mb-0">Total Notifikasi</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                    <i class="bi bi-check-circle text-success fs-4"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold text-success mb-1"><?= $read_count ?></h3>
                            <p class="text-muted mb-0">Sudah Dibaca</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                    <i class="bi bi-clock text-warning fs-4"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold text-warning mb-1"><?= $unread_count ?></h3>
                            <p class="text-muted mb-0">Belum Dibaca</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <div class="rounded-circle bg-info bg-opacity-10 p-3">
                                    <i class="bi bi-info-circle text-info fs-4"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold text-info mb-1"><?= $type_counts['info'] ?></h3>
                            <p class="text-muted mb-0">Info</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications Table -->
            <div class="card border-0 shadow-sm fade-in-up">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0 text-gold">
                        <i class="bi bi-list-ul me-2"></i>Daftar Notifikasi
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="border-0 ps-4">ID</th>
                                    <th class="border-0">Penerima</th>
                                    <th class="border-0">Judul</th>
                                    <th class="border-0">Pesan</th>
                                    <th class="border-0">Tipe</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Tanggal</th>
                                    <th class="border-0 pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="border-bottom">
                                    <td class="ps-4 fw-bold"><?= $row['id'] ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle me-2 text-primary"></i>
                                            <span><?= htmlspecialchars($row['nama_lengkap']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-semibold"><?= htmlspecialchars($row['judul']) ?></span>
                                    </td>
                                    <td>
                                        <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                            <span class="text-muted"><?= htmlspecialchars(substr($row['pesan'], 0, 50)) ?>...</span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = '';
                                        $icon = '';
                                        switch($row['tipe']) {
                                            case 'info': 
                                                $badge_class = 'bg-info'; 
                                                $icon = 'info-circle';
                                                break;
                                            case 'success': 
                                                $badge_class = 'bg-success'; 
                                                $icon = 'check-circle';
                                                break;
                                            case 'warning': 
                                                $badge_class = 'bg-warning'; 
                                                $icon = 'exclamation-triangle';
                                                break;
                                            case 'error': 
                                                $badge_class = 'bg-danger'; 
                                                $icon = 'x-circle';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $badge_class ?> px-3 py-2">
                                            <i class="bi bi-<?= $icon ?> me-1"></i>
                                            <?= ucfirst($row['tipe']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $row['status'] == 'dibaca' ? 'bg-success' : 'bg-secondary' ?> px-3 py-2">
                                            <i class="bi bi-<?= $row['status'] == 'dibaca' ? 'check-circle' : 'clock' ?> me-1"></i>
                                            <?= ucwords(str_replace('_', ' ', $row['status'])) ?>
                                        </span>
                                    </td>
                                    <td class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        <?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?>
                                    </td>
                                    <td class="pe-4">
                                        <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Hapus notifikasi ini?')"
                                           title="Hapus Notifikasi">
                                            <i class="bi bi-trash me-1"></i>Hapus
                                        </a>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-bell text-muted" style="font-size: 4rem;"></i>
                        <h5 class="text-muted mt-3">Belum ada notifikasi</h5>
                        <p class="text-muted">Notifikasi yang dikirim akan muncul di sini</p>
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
                        <i class="bi bi-bell me-2"></i>Kirim Notifikasi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-people me-1"></i>Penerima
                            </label>
                            <select class="form-control" name="user_id" required>
                                <option value="">Pilih Penerima</option>
                                <option value="all">📢 Semua Tamu</option>
                                <?php 
                                mysqli_data_seek($users_result, 0);
                                while ($user = mysqli_fetch_assoc($users_result)): 
                                ?>
                                    <option value="<?= $user['id'] ?>"><?= htmlspecialchars($user['nama_lengkap']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-type me-1"></i>Judul
                            </label>
                            <input type="text" class="form-control" name="judul" placeholder="Masukkan judul notifikasi" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-chat-text me-1"></i>Pesan
                            </label>
                            <textarea class="form-control" name="pesan" rows="4" placeholder="Masukkan isi pesan notifikasi" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="bi bi-tag me-1"></i>Tipe Notifikasi
                            </label>
                            <div class="btn-group w-100 mt-2" role="group" aria-label="Tipe notifikasi">
                                <input type="radio" class="btn-check" name="tipe" id="btninfo" value="info" required>
                                <label class="btn btn-outline-info" for="btninfo">
                                    <i class="bi bi-info-circle me-1"></i>Info
                                </label>
                                
                                <input type="radio" class="btn-check" name="tipe" id="btnsuccess" value="success" required>
                                <label class="btn btn-outline-success" for="btnsuccess">
                                    <i class="bi bi-check-circle me-1"></i>Success
                                </label>
                                
                                <input type="radio" class="btn-check" name="tipe" id="btnwarning" value="warning" required>
                                <label class="btn btn-outline-warning" for="btnwarning">
                                    <i class="bi bi-exclamation-triangle me-1"></i>Warning
                                </label>
                                
                                <input type="radio" class="btn-check" name="tipe" id="btnerror" value="error" required>
                                <label class="btn btn-outline-danger" for="btnerror">
                                    <i class="bi bi-x-circle me-1"></i>Error
                                </label>
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Tips:</strong> Pastikan pesan yang dikirim jelas dan informatif untuk memberikan pengalaman terbaik kepada tamu.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </button>
                        <button type="submit" name="send_notification" class="btn btn-primary">
                            <i class="bi bi-send me-1"></i>Kirim Notifikasi
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