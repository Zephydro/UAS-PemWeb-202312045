<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil semua kamar yang tersedia
$query = "SELECT * FROM rooms WHERE status = 'tersedia' ORDER BY harga_per_malam ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kamar - HotelEase</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
                <h1 class="hero-title fade-in-up">Daftar Kamar</h1>
                <p class="text-muted fade-in-up">Pilih kamar yang sesuai dengan kebutuhan dan kenyamanan Anda</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($room = mysqli_fetch_assoc($result)): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 fade-in-up facility-card">
                        <?php if (!empty($room['foto'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($room['foto']); ?>" 
                                 class="card-img-top room-image" 
                                 alt="<?= htmlspecialchars($room['tipe_kamar']); ?>">
                        <?php else: ?>
                            <img src="../assets/img/<?= strtolower(str_replace(' ', '', $room['tipe_kamar'])); ?>.jpg" 
                                 class="card-img-top room-image" 
                                 alt="<?= htmlspecialchars($room['tipe_kamar']); ?>"
                                 onerror="this.src='../assets/img/default-room.jpg'">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-gold fw-bold"><?= htmlspecialchars($room['tipe_kamar']); ?></h5>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-primary me-2">
                                    <i class="bi bi-door-closed me-1"></i>Kamar <?= htmlspecialchars($room['nomor_kamar']); ?>
                                </span>
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Tersedia
                                </span>
                            </div>
                            <p class="card-text text-muted mb-3"><?= htmlspecialchars($room['deskripsi']); ?></p>
                            
                            <!-- Room Features -->
                            <div class="facility-hours mb-3">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="info-item">
                                            <i class="bi bi-people-fill text-gold me-2"></i>
                                            <span class="small">Max <?= $room['kapasitas']; ?> tamu</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-item">
                                            <i class="bi bi-wifi text-gold me-2"></i>
                                            <span class="small">WiFi Gratis</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-item">
                                            <i class="bi bi-tv text-gold me-2"></i>
                                            <span class="small">TV LED</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="info-item">
                                            <i class="bi bi-snow text-gold me-2"></i>
                                            <span class="small">AC</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="text-center w-100">
                                        <h4 class="text-gold fw-bold mb-0">
                                            Rp <?= number_format($room['harga_per_malam'], 0, ',', '.'); ?>
                                        </h4>
                                        <small class="text-muted">per malam</small>
                                    </div>
                                </div>
                                <a href="booking.php?room_id=<?= $room['id']; ?>" 
                                   class="btn btn-primary w-100">
                                    <i class="bi bi-calendar-check me-2"></i>Booking Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="empty-state text-center py-5">
                        <i class="bi bi-door-open display-1 text-gold"></i>
                        <h4 class="text-gold mt-3">Tidak Ada Kamar Tersedia</h4>
                        <p class="text-muted">Maaf, saat ini semua kamar sedang terisi. Silakan coba lagi nanti atau hubungi customer service untuk informasi lebih lanjut.</p>
                        <a href="../contact.php" class="btn btn-primary">
                            <i class="bi bi-telephone me-2"></i>Hubungi Customer Service
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/user.js"></script>

<style>
.room-image {
    height: 200px;
    object-fit: cover;
    border-radius: var(--border-radius) var(--border-radius) 0 0;
    transition: var(--transition-smooth);
}

.facility-card:hover .room-image {
    transform: scale(1.05);
}

.info-item {
    display: flex;
    align-items: center;
    padding: 0.25rem 0;
    border-bottom: none;
    transition: var(--transition-fast);
}

.info-item:hover {
    color: var(--primary-gold);
}

.facility-hours {
    background: linear-gradient(135deg, var(--accent-cream) 0%, #ffffff 100%);
    border-radius: var(--border-radius);
    padding: 1rem;
    margin-top: 1rem;
    border: 1px solid rgba(212, 175, 55, 0.1);
}

.empty-state {
    padding: 4rem 2rem;
    background: rgba(255, 255, 255, 0.9);
    border-radius: var(--border-radius-lg);
    box-shadow: var(--shadow-soft);
    margin: 2rem 0;
}

.text-gold {
    color: var(--primary-gold) !important;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-medium);
}
</style>

</body>
</html>
