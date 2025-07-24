<?php
session_start();
require '../config/koneksi.php';
require '../config/helpers.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['update_password'])) {
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - HotelEase</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <!-- Hamburger panel will be injected by hamburger-universal.js -->
    <?php require('sidebar.php'); ?>

<!-- Main Content -->
<main class="main-content">
    <div class="container-fluid p-4">
        <div class="row mb-4">
            <div class="col">
                <h1 class="hero-title fade-in-up">Profil Saya</h1>
                <p class="text-muted fade-in-up">Kelola informasi pribadi dan keamanan akun Anda</p>
            </div>
        </div>

        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show fade-in-up" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show fade-in-up" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Profile Information -->
            <div class="col-lg-8">
                <div class="card h-100 fade-in-up facility-card">
                    <div class="card-header">
                        <h5 class="mb-0 text-gold">
                            <i class="bi bi-person-circle me-2"></i>Informasi Pribadi
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama" 
                                           value="<?= htmlspecialchars($user['nama_lengkap']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="no_hp" class="form-label">Nomor Telepon</label>
                                    <input type="tel" class="form-control" id="no_hp" name="no_hp" 
                                           value="<?= htmlspecialchars($user['no_hp'] ?? ''); ?>">
                                </div>
                                <div class="col-md-6">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($user['username']); ?>" disabled>
                                    <div class="form-text">Username tidak dapat diubah</div>
                                </div>
                                <div class="col-12">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3"><?= htmlspecialchars($user['alamat'] ?? ''); ?></textarea>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-circle me-1"></i>Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Account Security & Info -->
            <div class="col-lg-4">
                <!-- Profile Avatar Card -->
                <div class="card h-100 mb-4 fade-in-up facility-card">
                    <div class="card-body text-center">
                        <div class="profile-avatar mx-auto mb-3">
                            <?= strtoupper(substr($user['nama_lengkap'], 0, 1)); ?>
                        </div>
                        <h5 class="fw-bold text-gold"><?= htmlspecialchars($user['nama_lengkap']); ?></h5>
                        <p class="text-muted mb-2"><?= htmlspecialchars($user['email'] ?? ''); ?></p>
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>
                            Bergabung <?= date('d M Y', strtotime($user['created_at'])); ?>
                        </small>
                    </div>
                </div>

                <!-- Change Password -->
                <div class="card h-100 fade-in-up facility-card">
                    <div class="card-header">
                        <h5 class="mb-0 text-gold">
                            <i class="bi bi-shield-lock me-2"></i>Keamanan Akun
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Password Saat Ini</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="current_password" 
                                           name="current_password" required>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('current_password')">
                                        <i class="bi bi-eye" id="current_password_icon"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="new_password" 
                                           name="new_password" required minlength="6">
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('new_password')">
                                        <i class="bi bi-eye" id="new_password_icon"></i>
                                    </button>
                                </div>
                                <div class="form-text">Minimal 6 karakter</div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirm_password" 
                                           name="confirm_password" required>
                                    <button class="btn btn-outline-secondary" type="button" 
                                            onclick="togglePassword('confirm_password')">
                                        <i class="bi bi-eye" id="confirm_password_icon"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <button type="submit" name="update_password" class="btn btn-warning w-100">
                                <i class="bi bi-key me-1"></i>Ubah Password
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/user.js"></script>

<script>
// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '_icon');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        field.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = this.value;
    
    if (newPassword !== confirmPassword) {
        this.setCustomValidity('Password tidak cocok');
    } else {
        this.setCustomValidity('');
    }
});

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
});
</script>

<style>
.profile-avatar {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-gold), #B8860B);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: bold;
    color: white;
    box-shadow: var(--shadow-medium);
    margin: 0 auto;
}

.form-control:focus {
    border-color: var(--primary-gold);
    box-shadow: 0 0 0 0.2rem rgba(212, 175, 55, 0.25);
}

.input-group .btn:hover {
    background-color: var(--primary-gold);
    border-color: var(--primary-gold);
    color: white;
}

.btn-warning {
    background: linear-gradient(135deg, #ffc107, #fd7e14);
    border: none;
    color: white;
    font-weight: 600;
}

.btn-warning:hover {
    background: linear-gradient(135deg, #fd7e14, #ffc107);
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
}

.facility-card .card-header {
    background: linear-gradient(135deg, var(--accent-cream), white);
    border-bottom: 1px solid rgba(212, 175, 55, 0.2);
}

.text-gold {
    color: var(--primary-gold) !important;
}

.alert {
    border-radius: var(--border-radius);
    border: none;
    box-shadow: var(--shadow-soft);
}

.form-text {
    color: var(--text-light);
    font-size: 0.875rem;
}

.was-validated .form-control:invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.was-validated .form-control:valid {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}
</style>

</body>
</html>
