<?php
session_start();
require 'config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: auth/login.php");
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
    <title>Daftar Kamar - HotelEase</title>
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
        .room-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        .room-image {
            height: 250px;
            object-fit: cover;
        }
        .price-tag {
            background: linear-gradient(45deg, #00362A, #004d3a);
            color: white;
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <?php include 'user/sidebar.php'; ?>

    <!-- Content -->
    <div class="main-content p-4">
        <div class="mb-4">
            <h1>Daftar Kamar</h1>
            <p class="lead">Pilih kamar yang sesuai dengan kebutuhan Anda</p>
        </div>

        <div class="row g-4">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($room = mysqli_fetch_assoc($result)): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card room-card h-100 bg-panel">
                        <?php if (!empty($room['foto'])): ?>
                            <img src="uploads/<?= htmlspecialchars($room['foto']); ?>" 
                                 class="card-img-top room-image" 
                                 alt="<?= htmlspecialchars($room['tipe_kamar']); ?>">
                        <?php else: ?>
                            <img src="assets/img/<?= strtolower(str_replace(' ', '', $room['tipe_kamar'])); ?>.jpg" 
                                 class="card-img-top room-image" 
                                 alt="<?= htmlspecialchars($room['tipe_kamar']); ?>"
                                 onerror="this.src='assets/img/default-room.jpg'">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title text-green"><?= htmlspecialchars($room['tipe_kamar']); ?></h5>
                            <p class="text-muted mb-2"><strong>Nomor Kamar:</strong> <?= htmlspecialchars($room['nomor_kamar']); ?></p>
                            <p class="card-text text-muted mb-3"><?= htmlspecialchars($room['deskripsi']); ?></p>
                            
                            <div class="mb-3">
                                <div class="row text-sm">
                                    <div class="col-6">
                                        <i class="bi bi-people-fill text-green"></i>
                                        <span>Max <?= $room['kapasitas']; ?> tamu</span>
                                    </div>
                                    <div class="col-6">
                                        <i class="bi bi-wifi text-green"></i>
                                        <span>WiFi Gratis</span>
                                    </div>
                                </div>
                                <div class="row text-sm mt-2">
                                    <div class="col-6">
                                        <i class="bi bi-tv text-green"></i>
                                        <span>TV LED</span>
                                    </div>
                                    <div class="col-6">
                                        <i class="bi bi-snow text-green"></i>
                                        <span>AC</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="price-tag">
                                        Rp <?= number_format($room['harga_per_malam'], 0, ',', '.'); ?>/malam
                                    </span>
                                </div>
                                <a href="auth/booking.php?room_id=<?= $room['id']; ?>" 
                                   class="btn btn-dark-green w-100">
                                    <i class="bi bi-calendar-check me-2"></i>Booking Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Maaf, saat ini tidak ada kamar yang tersedia.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
