<?php
session_start();

// Simple check - if no session, redirect to login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

// If role is set and it's not user, redirect  
if (isset($_SESSION['role']) && $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard User - HotelEase</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
                <h2 class="fw-bold text-gold mb-2 fade-in-up">Halo, <?= htmlspecialchars($_SESSION['username']) ?>!</h2>
                <p class="text-muted fade-in-up">Selamat datang kembali di HotelEase. Temukan kenyamanan seperti di rumah sendiri.</p>
            </div>
        </div>
        
        <?php if (isset($_GET['booking']) && $_GET['booking'] == 'success' && isset($_SESSION['ringkasan_booking'])): ?>
        <div class="alert alert-success alert-dismissible fade show fade-in-up" role="alert">
            <h5><i class="bi bi-check-circle-fill me-2"></i>Booking Berhasil!</h5>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-1"><strong>ID Booking:</strong> #<?= $_SESSION['ringkasan_booking']['booking_id']; ?></p>
                    <p class="mb-1"><strong>Nama Pemesan:</strong> <?= htmlspecialchars($_SESSION['ringkasan_booking']['nama']); ?></p>
                    <p class="mb-1"><strong>Tipe Kamar:</strong> <?= htmlspecialchars($_SESSION['ringkasan_booking']['kamar']); ?></p>
                    <p class="mb-1"><strong>Jumlah Tamu:</strong> <?= $_SESSION['ringkasan_booking']['tamu']; ?> orang</p>
                </div>
                <div class="col-md-6">
                    <p class="mb-1"><strong>Jumlah Malam:</strong> <?= $_SESSION['ringkasan_booking']['malam']; ?> malam</p>
                    <p class="mb-1"><strong>Check-in:</strong> <?= date('d/m/Y', strtotime($_SESSION['ringkasan_booking']['checkin'])); ?></p>
                    <p class="mb-1"><strong>Check-out:</strong> <?= date('d/m/Y', strtotime($_SESSION['ringkasan_booking']['checkout'])); ?></p>
                    <p class="mb-1"><strong>Metode Pembayaran:</strong> <?= htmlspecialchars($_SESSION['ringkasan_booking']['metode_pembayaran']); ?></p>
                </div>
            </div>
            <hr>
            <h6 class="text-success"><strong>Total Harga: Rp <?= number_format($_SESSION['ringkasan_booking']['harga'], 0, ',', '.'); ?></strong></h6>
            <small class="text-muted">Silakan lakukan pembayaran sesuai metode yang dipilih. Status booking dapat dilihat di menu Riwayat.</small>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['ringkasan_booking']); ?>
        <?php endif; ?>

        <!-- Quick Actions -->
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-calendar-plus-fill text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Booking Baru</h6>
                        <a href="kamar.php" class="btn btn-primary">
                            <i class="bi bi-plus-lg me-1"></i>Pesan Kamar
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-clock-history-fill text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Riwayat</h6>
                        <a href="riwayat.php" class="btn btn-outline-primary">
                            <i class="bi bi-list-ul me-1"></i>Lihat Riwayat
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-person-circle-fill text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Profil</h6>
                        <a href="profil.php" class="btn btn-outline-primary">
                            <i class="bi bi-gear me-1"></i>Kelola Profil
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="card h-100 fade-in-up">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="bi bi-stars-fill text-gold" style="font-size: 3rem;"></i>
                        </div>
                        <h6 class="text-muted mb-2">Fasilitas</h6>
                        <a href="fasilitas.php" class="btn btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>Lihat Fasilitas
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Kamar -->
        <section class="mb-5">
            <h2 class="text-center mb-4 text-gold fade-in-up">Pilih Kamar Favoritmu</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 fade-in-up">
                        <img src="../assets/img/superior.jpg" class="card-img-top" alt="Superior Room" style="height: 250px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-gold">Superior Room</h5>
                            <ul class="list-unstyled small text-muted mb-3">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Pegunungan, 1-2 Tamu</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Balkon, TV</li>
                            </ul>
                            <div class="mt-auto">
                                <p class="text-gold fw-bold fs-5 mb-3">Rp 450.000 / malam</p>
                                <a href="kamar.php" class="btn btn-primary w-100">
                                    <i class="bi bi-eye me-1"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 fade-in-up">
                        <img src="../assets/img/deluxe.jpg" class="card-img-top" alt="Deluxe Room" style="height: 250px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-gold">Deluxe Room</h5>
                            <ul class="list-unstyled small text-muted mb-3">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Taman, 2-3 Tamu</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Bathtub, AC</li>
                            </ul>
                            <div class="mt-auto">
                                <p class="text-gold fw-bold fs-5 mb-3">Rp 650.000 / malam</p>
                                <a href="kamar.php" class="btn btn-primary w-100">
                                    <i class="bi bi-eye me-1"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 fade-in-up">
                        <img src="../assets/img/premium.jpg" class="card-img-top" alt="Premium Room" style="height: 250px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold text-gold">Premium Room</h5>
                            <ul class="list-unstyled small text-muted mb-3">
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Suite, 3-4 Tamu</li>
                                <li><i class="bi bi-check-circle-fill text-success me-2"></i>Lounge, Breakfast</li>
                            </ul>
                            <div class="mt-auto">
                                <p class="text-gold fw-bold fs-5 mb-3">Rp 950.000 / malam</p>
                                <a href="kamar.php" class="btn btn-primary w-100">
                                    <i class="bi bi-eye me-1"></i>Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Fasilitas (Slider) -->
        <section class="mb-5">
            <h2 class="text-center text-gold mb-4 fade-in-up">Fasilitas Unggulan</h2>
            <div id="fasilitasCarousel" class="carousel slide fade-in-up" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <div class="position-relative">
                            <img src="../assets/img/pool1.jpg" class="d-block w-100 object-fit-cover" style="height:300px; border-radius: 1rem;" alt="Kolam Renang">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-start justify-content-center">
                                <span class="bg-dark bg-opacity-50 text-white px-4 py-2 rounded-bottom fs-5 fw-semibold mt-2">Kolam Renang</span>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="position-relative">
                            <img src="../assets/img/restaurant.jpg" class="d-block w-100 object-fit-cover" style="height:300px; border-radius: 1rem;" alt="Restoran">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-start justify-content-center">
                                <span class="bg-dark bg-opacity-50 text-white px-4 py-2 rounded-bottom fs-5 fw-semibold mt-2">Restoran</span>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <div class="position-relative">
                            <img src="../assets/img/spa.jpg" class="d-block w-100 object-fit-cover" style="height:300px; border-radius: 1rem;" alt="Spa">
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-start justify-content-center">
                                <span class="bg-dark bg-opacity-50 text-white px-4 py-2 rounded-bottom fs-5 fw-semibold mt-2">Spa & Wellness</span>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#fasilitasCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#fasilitasCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </section>

        <!-- Testimoni -->
        <section class="mb-5">
            <h2 class="text-center text-gold mb-4 fade-in-up">Testimoni Tamu</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100 fade-in-up">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-circle me-3">A</div>
                                <div>
                                    <h6 class="mb-0">Andi Pratama</h6>
                                    <div class="text-warning">
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted">"Pelayanan yang sangat memuaskan! Kamar bersih dan nyaman. Pasti akan kembali lagi."</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 fade-in-up">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-circle me-3">S</div>
                                <div>
                                    <h6 class="mb-0">Sari Dewi</h6>
                                    <div class="text-warning">
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted">"Lokasi strategis dan fasilitas lengkap. Staff ramah dan profesional. Highly recommended!"</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 fade-in-up">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-circle me-3">B</div>
                                <div>
                                    <h6 class="mb-0">Budi Santoso</h6>
                                    <div class="text-warning">
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star-fill"></i>
                                        <i class="bi bi-star"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="text-muted">"Pengalaman menginap yang tak terlupakan. Makanan enak dan view yang indah."</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

<!-- Include JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/user.js"></script>

</body>
</html>
