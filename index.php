<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hotel Ease - Selamat Datang</title>
    <link rel="stylesheet" href="assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <!-- Hero Section with Background -->
    <div class="hero-bg">
        <div class="container">
            <div class="hero-content shadow-lg fade-in-up">
                <h1 class="hero-title display-4 mb-4">Selamat Datang di Hotel Ease</h1>
                <p class="text-muted fs-5 mb-4">Nikmati kemewahan dan kenyamanan dengan pemandangan terbaik. Booking sekarang untuk pengalaman menginap tak terlupakan.</p>
                <div class="d-flex gap-3 flex-wrap">
                    <a href="auth/login.php" class="btn btn-primary px-4 py-3 rounded-pill">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </a>
                    <a href="auth/register.php" class="btn btn-outline-dark rounded-pill px-4 py-3">
                        <i class="bi bi-person-plus me-2"></i>Daftar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Highlight Section -->
    <div class="container pb-5" style="margin-top:-60px; position:relative; z-index:3;">
        <div class="row text-center">
            <div class="col-md-4">
                <div class="highlight-card fade-in-up">
                    <img src="assets/img/icon_kamar.png" alt="Icon Kamar" class="mb-3" style="width:72px; height:72px; opacity:0.85;">
                    <h5 class="fw-bold text-gold">Kamar Nyaman</h5>
                    <p class="text-secondary">Berbagai pilihan kamar mulai dari superior, deluxe hingga premium suite.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="highlight-card fade-in-up">
                    <img src="assets/img/icon-tree.png" alt="Icon Pemandangan" class="mb-3" style="width:72px; height:72px; opacity:0.85;">
                    <h5 class="fw-bold text-gold">Pemandangan Indah</h5>
                    <p class="text-secondary">Dari balkon, Anda bisa menikmati pemandangan pegunungan atau taman hijau.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="highlight-card fade-in-up">
                    <img src="assets/img/icon-booking.png" alt="Icon Booking" class="mb-3" style="width:72px; height:72px; opacity:0.85;">
                    <h5 class="fw-bold text-gold">Booking Mudah</h5>
                    <p class="text-secondary">Proses reservasi online cepat, mudah, dan aman untuk semua tamu.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Include JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/hotel-ease.js"></script>
</body>
</html>
