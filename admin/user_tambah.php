<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include '../config/koneksi.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap = $_POST['nama_lengkap'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = mysqli_real_escape_string($conn, $_POST['password']); // TANPA HASH
    $role_id = $_POST['role_id'];

    // Cek apakah username atau email sudah ada
    $check_query = "SELECT id FROM users WHERE username = ? OR email = ?";
    $check_stmt = mysqli_prepare($conn, $check_query);
    mysqli_stmt_bind_param($check_stmt, "ss", $username, $email);
    mysqli_stmt_execute($check_stmt);
    $check_result = mysqli_stmt_get_result($check_stmt);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error = "Username atau email sudah digunakan!";
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO users (nama_lengkap, username, email, password, role_id) VALUES (?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssssi", $nama_lengkap, $username, $email, $password, $role_id);
        
        if (mysqli_stmt_execute($stmt)) {
            header("Location: users.php");
            exit;
        } else {
            $error = "Gagal menambahkan pengguna!";
        }
    }
}

// Cek jika form disubmit

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pengguna - HotelEase</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="admin-body">
    <div class="main-content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
                <div>
                    <h2 class="fw-bold text-gold mb-1">
                        <i class="bi bi-person-plus me-2"></i>Tambah Pengguna
                    </h2>
                    <p class="text-muted mb-0">Buat akun pengguna baru</p>
                </div>
                <a href="users.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Kembali
                </a>
            </div>
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
            <!-- Alert Messages -->
            <?php if (isset($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm fade-in-up" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Add Form -->
            <div class="card border-0 shadow-lg fade-in-up">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-bold">
                        <i class="bi bi-person-plus-fill me-2"></i>Form Tambah Pengguna
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="user_tambah.php" method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nama_lengkap" class="form-label fw-bold">
                                    <i class="bi bi-person me-1"></i>Nama Lengkap
                                </label>
                                <input type="text" class="form-control" name="nama_lengkap" 
                                       placeholder="Masukkan nama lengkap" required>
                            </div>

                            <div class="col-md-6">
                                <label for="username" class="form-label fw-bold">
                                    <i class="bi bi-at me-1"></i>Username
                                </label>
                                <input type="text" class="form-control" name="username" 
                                       placeholder="Masukkan username" required>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold">
                                    <i class="bi bi-envelope me-1"></i>Email
                                </label>
                                <input type="email" class="form-control" name="email" 
                                       placeholder="Masukkan email" required>
                            </div>

                            <div class="col-md-6">
                                <label for="password" class="form-label fw-bold">
                                    <i class="bi bi-key me-1"></i>Password
                                </label>
                                <input type="password" class="form-control" name="password" 
                                       placeholder="Masukkan password" required>
                            </div>

                            <div class="col-12">
                                <label for="role_id" class="form-label fw-bold">
                                    <i class="bi bi-shield-check me-1"></i>Role
                                </label>
                                <select name="role_id" class="form-select" required>
                                    <option value="">-- Pilih Role --</option>
                                    <?php
                                    // Ambil role dari database
                                    $result = mysqli_query($conn, "SELECT * FROM roles");
                                    while ($r = mysqli_fetch_assoc($result)) {
                                        echo "<option value='{$r['id']}'>{$r['nama_role']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" name="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Tambah Pengguna
                            </button>
                            <a href="users.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/hotel-ease.js"></script>
</body>
</html>
