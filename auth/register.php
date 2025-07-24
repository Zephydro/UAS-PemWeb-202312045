<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Tamu - HotelEase</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="login-page">

    <div class="container d-flex align-items-center justify-content-center vh-100">
        <div class="login-card shadow-lg fade-in-up">
            <div class="text-center mb-4">
                <div class="mb-3">
                    <i class="bi bi-person-plus-fill text-gold" style="font-size: 4rem;"></i>
                </div>
                <h3 class="mb-2">Daftar Sebagai Tamu</h3>
                <p class="text-muted">Silakan lengkapi data Anda untuk bergabung</p>
            </div>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle me-2"></i>
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form action="proses_register.php" method="post">
                <div class="mb-3">
                    <label for="nama" class="form-label">
                        <i class="bi bi-person me-2"></i>Nama Lengkap
                    </label>
                    <input type="text" class="form-control" name="nama" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope me-2"></i>Email Aktif
                    </label>
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">
                        <i class="bi bi-at me-2"></i>Username
                    </label>
                    <input type="text" class="form-control" name="username" required>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">
                        <i class="bi bi-lock me-2"></i>Password
                    </label>
                    <input type="password" class="form-control" name="password" required>
                </div>

                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-person-plus me-2"></i>Daftar Sekarang
                </button>

                <div class="text-center">
                    <a href="login.php" class="text-decoration-none d-block mb-2">
                        <i class="bi bi-box-arrow-in-right me-1"></i>Sudah punya akun? Login di sini
                    </a>
                    <a href="../index.php" class="text-decoration-none">
                        <i class="bi bi-house me-1"></i>Kembali ke Beranda
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Include JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/hotel-ease.js"></script>

</body>
</html>
