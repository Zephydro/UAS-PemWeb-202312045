<?php
session_start();
require 'config/koneksi.php';
require 'config/helpers.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data user
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);
    
    // Cek apakah email sudah digunakan user lain
    $check_email = "SELECT id FROM users WHERE email = ? AND id != ?";
    $check_stmt = $conn->prepare($check_email);
    $check_stmt->bind_param("si", $email, $user_id);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        $_SESSION['error'] = "Email sudah digunakan oleh user lain.";
    } else {
        // Update data user
        $update_query = "UPDATE users SET nama_lengkap = ?, email = ?, no_hp = ?, alamat = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bind_param("ssssi", $nama_lengkap, $email, $no_hp, $alamat, $user_id);
        
        if ($update_stmt->execute()) {
            // Update session username
            $_SESSION['username'] = $nama_lengkap;
            
            // Log aktivitas
            logActivity($conn, $user_id, 'profile_update', 'Berhasil mengupdate profil', $_SERVER['REMOTE_ADDR']);
            
            // Kirim notifikasi
            sendNotification($conn, $user_id, 'Profil Diperbarui', 'Profil Anda telah berhasil diperbarui.', 'success');
            
            $_SESSION['success'] = "Profil berhasil diperbarui.";
            
            // Refresh data user
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
        } else {
            $_SESSION['error'] = "Gagal memperbarui profil.";
        }
    }
}

// Proses update password
if (isset($_POST['update_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Konfirmasi password tidak cocok.";
    } elseif (strlen($new_password) < 6) {
        $_SESSION['error'] = "Password minimal 6 karakter.";
    } elseif ($current_password !== $user['password']) {
        $_SESSION['error'] = "Password lama tidak benar.";
    } else {
        $plain_password = mysqli_real_escape_string($conn, $new_password);
        $update_password_query = "UPDATE users SET password = ? WHERE id = ?";
        $update_password_stmt = $conn->prepare($update_password_query);
        $update_password_stmt->bind_param("si", $plain_password, $user_id);
        
        if ($update_password_stmt->execute()) {
            logActivity($conn, $user_id, 'password_change', 'Berhasil mengubah password', $_SERVER['REMOTE_ADDR']);
            sendNotification($conn, $user_id, 'Password Diubah', 'Password Anda telah berhasil diubah.', 'info');
            $_SESSION['success'] = "Password berhasil diubah.";
        } else {
            $_SESSION['error'] = "Gagal mengubah password.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya - HotelEase</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Krona+One&family=Montserrat+Alternates:wght@400;600&display=swap" rel="stylesheet">
    
    <style>
        body {
            background-color: #ECEDE3;
            font-family: 'Montserrat Alternates', sans-serif;
        }
        h1, h2, h3 {
            font-family: 'Krona One', sans-serif;
            color: #00362A;
        }
        .text-green {
            color: #00362A;
        }
        .bg-panel {
            background-color: #E0E1D1;
        }
        .btn-dark-green {
            background-color: #00362A;
            border: none;
            color: white;
        }
        .btn-dark-green:hover {
            background-color: #000453;
        }
        .sidebar {
            width: 240px;
            background-color: #00362A;
            color: white;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            overflow-y: auto;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 12px 0;
            border-radius: 8px;
            margin: 5px 0;
            transition: all 0.3s ease;
        }
        .sidebar a:hover {
            background-color: rgba(255,255,255,0.1);
            color: #C6C7B3;
            padding-left: 10px;
        }
        .sidebar a.active {
            background-color: rgba(255,255,255,0.2);
            font-weight: 600;
        }
        .main-content {
            margin-left: 240px;
            min-height: 100vh;
        }
        .profile-avatar {
            width: 120px;
            height: 120px;
            background: linear-gradient(45deg, #00362A, #004d3a);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            margin: 0 auto 1rem;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <?php include 'user/sidebar.php'; ?>

    <!-- Content -->
    <div class="main-content p-4">
        <div class="mb-4">
            <h1>Profil Saya</h1>
            <p class="lead">Kelola informasi profil dan keamanan akun Anda</p>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row">
            <!-- Profile Info -->
            <div class="col-md-4">
                <div class="card bg-panel">
                    <div class="card-body text-center">
                        <div class="profile-avatar">
                            <?= strtoupper(substr($user['nama_lengkap'], 0, 1)); ?>
                        </div>
                        <h5 class="text-green"><?= htmlspecialchars($user['nama_lengkap']); ?></h5>
                        <p class="text-muted"><?= htmlspecialchars($user['email'] ?? ''); ?></p>
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>
                            Bergabung <?= date('d M Y', strtotime($user['created_at'])); ?>
                        </small>
                    </div>
                </div>
            </div>

            <!-- Profile Form -->
            <div class="col-md-8">
                <div class="card bg-panel">
                    <div class="card-header">
                        <h5 class="text-green mb-0">
                            <i class="bi bi-person-gear me-2"></i>Informasi Profil
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama" 
                                           value="<?= htmlspecialchars($user['nama_lengkap']); ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="no_hp" class="form-label">No. HP</label>
                                    <input type="text" class="form-control" id="no_hp" name="no_hp" 
                                           value="<?= htmlspecialchars($user['no_hp'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']); ?>" disabled>
                                    <small class="text-muted">Username tidak dapat diubah</small>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= htmlspecialchars($user['alamat'] ?? ''); ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-dark-green">
                                <i class="bi bi-check-lg me-2"></i>Simpan Perubahan
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="card bg-panel mt-4">
                    <div class="card-header">
                        <h5 class="text-green mb-0">
                            <i class="bi bi-shield-lock me-2"></i>Ubah Password
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Password Lama</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="new_password" class="form-label">Password Baru</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" 
                                           minlength="6" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           minlength="6" required>
                                </div>
                            </div>
                            <button type="submit" name="update_password" class="btn btn-warning">
                                <i class="bi bi-key me-2"></i>Ubah Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Validasi konfirmasi password
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (newPassword !== confirmPassword) {
        this.setCustomValidity('Password tidak cocok');
    } else {
        this.setCustomValidity('');
    }
});
</script>
</body>
</html>
