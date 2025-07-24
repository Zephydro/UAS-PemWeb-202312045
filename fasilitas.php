<?php
session_start();
require 'config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: auth/login.php");
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
        .facility-card {
            transition: transform 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .facility-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        .facility-image {
            height: 200px;
            object-fit: cover;
        }
        .facility-icon {
            font-size: 3rem;
            color: #00362A;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
<div class="d-flex">
    <?php include 'user/sidebar.php'; ?>

    <!-- Content -->
    <div class="main-content p-4">
        <div class="mb-4">
            <h1>Fasilitas Hotel</h1>
            <p class="lead">Nikmati berbagai fasilitas unggulan yang kami sediakan untuk kenyamanan Anda</p>
        </div>

        <div class="row g-4">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($facility = mysqli_fetch_assoc($result)): ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card facility-card h-100 bg-panel">
                        <div class="card-body text-center d-flex flex-column">
                            <div class="facility-icon">
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
                                <i class="bi <?= $icon; ?>"></i>
                            </div>
                            
                            <h5 class="card-title text-green mb-3"><?= htmlspecialchars($facility['nama_fasilitas']); ?></h5>
                            <p class="card-text text-muted mb-auto"><?= htmlspecialchars($facility['deskripsi']); ?></p>
                            
                            <?php if (!empty($facility['jam_operasional'])): ?>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    <?= htmlspecialchars($facility['jam_operasional']); ?>
                                </small>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle-fill me-2"></i>
                        Saat ini tidak ada fasilitas yang tersedia.
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Informasi Tambahan -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card bg-panel">
                    <div class="card-body">
                        <h3 class="text-green mb-4">Informasi Penting</h3>
                        <div class="row">
                            <div class="col-md-6">
                                <h5><i class="bi bi-clock text-green me-2"></i>Jam Operasional</h5>
                                <ul class="list-unstyled">
                                    <li>• Resepsionis: 24 jam</li>
                                    <li>• Restoran: 06:00 - 22:00</li>
                                    <li>• Kolam Renang: 06:00 - 20:00</li>
                                    <li>• Gym: 05:00 - 22:00</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5><i class="bi bi-info-circle text-green me-2"></i>Ketentuan</h5>
                                <ul class="list-unstyled">
                                    <li>• Fasilitas gratis untuk tamu hotel</li>
                                    <li>• Beberapa fasilitas memerlukan reservasi</li>
                                    <li>• Harap mematuhi jam operasional</li>
                                    <li>• Hubungi resepsionis untuk informasi lebih lanjut</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>