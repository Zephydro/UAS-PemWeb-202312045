<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil semua fasilitas yang aktif
$query = "SELECT * FROM fasilitas WHERE status = 'aktif' ORDER BY nama_fasilitas ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Fasilitas Hotel - HotelEase</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <!-- Hamburger panel will be injected by hamburger-universal.js -->
    <?php require('sidebar.php'); ?>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold text-gold mb-2 fade-in-up">Fasilitas Hotel</h2>
                <p class="text-muted fade-in-up">Nikmati berbagai fasilitas premium yang kami sediakan untuk kenyamanan Anda</p>
            </div>
        </div>

        <div class="row g-4">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($facility = mysqli_fetch_assoc($result)): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card facility-card shadow-lg h-100 fade-in-up">
                        <div class="card-body text-center d-flex flex-column">
                            <div class="facility-icon mb-3">
                                <?php
                                // Icon mapping berdasarkan nama fasilitas
                                $icons = [
                                    'kolam renang' => 'bi-water',
                                    'gym' => 'bi-heart-pulse',
                                    'spa' => 'bi-flower1',
                                    'restoran' => 'bi-cup-hot',
                                    'wifi' => 'bi-wifi',
                                    'parkir' => 'bi-car-front',
                                    'laundry' => 'bi-droplet',
                                    'meeting room' => 'bi-people',
                                    'business center' => 'bi-building',
                                    'bar' => 'bi-cup-straw'
                                ];
                                
                                $facility_name = strtolower($facility['nama_fasilitas']);
                                $icon = 'bi-star'; // default icon
                                
                                foreach ($icons as $key => $value) {
                                    if (strpos($facility_name, $key) !== false) {
                                        $icon = $value;
                                        break;
                                    }
                                }
                                ?>
                                <i class="bi <?= $icon; ?> text-gold"></i>
                            </div>
                            
                            <h5 class="card-title text-gold mb-3"><?= htmlspecialchars($facility['nama_fasilitas']); ?></h5>
                            <p class="card-text text-muted mb-auto flex-grow-1"><?= htmlspecialchars($facility['deskripsi']); ?></p>
                            
                            <?php if (!empty($facility['jam_operasional'])): ?>
                            <div class="facility-hours mt-auto">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="bi bi-clock me-2 text-primary"></i>
                                    <small class="text-muted">
                                        <strong>Jam Operasional:</strong> <?= htmlspecialchars($facility['jam_operasional']); ?>
                                    </small>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="text-center py-5 fade-in-up">
                        <div class="empty-state">
                            <i class="bi bi-building display-1 text-muted mb-4"></i>
                            <h3 class="text-gold mb-3">Fasilitas Sedang Diperbarui</h3>
                            <p class="text-muted">Kami sedang memperbarui informasi fasilitas. Silakan kembali lagi nanti.</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Hotel Information Section -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card shadow-lg fade-in-up">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6">
                                <h4 class="text-gold mb-4">
                                    <i class="bi bi-info-circle me-2"></i>Informasi Hotel
                                </h4>
                                <div class="info-list">
                                    <div class="info-item mb-3">
                                        <i class="bi bi-clock me-3 text-primary"></i>
                                        <div>
                                            <strong>Jam Operasional Resepsionis:</strong><br>
                                            <span class="text-muted">24 Jam (Setiap Hari)</span>
                                        </div>
                                    </div>
                                    <div class="info-item mb-3">
                                        <i class="bi bi-calendar-check me-3 text-success"></i>
                                        <div>
                                            <strong>Check-in:</strong><br>
                                            <span class="text-muted">14:00 WIB</span>
                                        </div>
                                    </div>
                                    <div class="info-item mb-3">
                                        <i class="bi bi-calendar-x me-3 text-warning"></i>
                                        <div>
                                            <strong>Check-out:</strong><br>
                                            <span class="text-muted">12:00 WIB</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <h4 class="text-gold mb-4">
                                    <i class="bi bi-shield-check me-2"></i>Kebijakan Hotel
                                </h4>
                                <div class="policy-list">
                                    <div class="policy-item mb-3">
                                        <i class="bi bi-check-circle me-2 text-success"></i>
                                        <span>WiFi gratis di seluruh area hotel</span>
                                    </div>
                                    <div class="policy-item mb-3">
                                        <i class="bi bi-check-circle me-2 text-success"></i>
                                        <span>Parkir gratis untuk tamu hotel</span>
                                    </div>
                                    <div class="policy-item mb-3">
                                        <i class="bi bi-check-circle me-2 text-success"></i>
                                        <span>Sarapan tersedia (berbayar)</span>
                                    </div>
                                    <div class="policy-item mb-3">
                                        <i class="bi bi-x-circle me-2 text-danger"></i>
                                        <span>Dilarang merokok di dalam kamar</span>
                                    </div>
                                    <div class="policy-item mb-3">
                                        <i class="bi bi-x-circle me-2 text-danger"></i>
                                        <span>Tidak diperbolehkan membawa hewan peliharaan</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-lg fade-in-up">
                    <div class="card-body text-center">
                        <h4 class="text-gold mb-4">
                            <i class="bi bi-telephone me-2"></i>Butuh Bantuan?
                        </h4>
                        <p class="text-muted mb-4">Tim customer service kami siap membantu Anda 24/7</p>
                        <div class="row g-3 justify-content-center">
                            <div class="col-auto">
                                <a href="tel:+6281234567890" class="btn btn-primary">
                                    <i class="bi bi-telephone me-2"></i>Hubungi Kami
                                </a>
                            </div>
                            <div class="col-auto">
                                <a href="mailto:info@hotelease.com" class="btn btn-outline-primary">
                                    <i class="bi bi-envelope me-2"></i>Email Kami
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/user.js"></script>

</body>
</html>