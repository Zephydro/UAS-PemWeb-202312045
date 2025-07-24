<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

$room_id = isset($_GET['room_id']) ? (int)$_GET['room_id'] : 0;

$query = "SELECT * FROM rooms WHERE id = $room_id AND status = 'tersedia'";
$result = mysqli_query($conn, $query);
$room = mysqli_fetch_assoc($result);

if (!$room) {
    echo "<div class='alert alert-danger text-center mt-5'>Kamar tidak ditemukan atau sudah tidak tersedia.</div>";
    exit;
}

// Ambil metode pembayaran yang aktif
$payment_methods_query = "SELECT * FROM metode_pembayaran WHERE status = 'aktif' ORDER BY nama_metode";
$payment_methods = mysqli_query($conn, $payment_methods_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Booking Kamar - HotelEase</title>
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
                <h2 class="fw-bold text-gold mb-2 fade-in-up">Booking Kamar</h2>
                <p class="text-muted fade-in-up">Lengkapi data booking untuk melanjutkan reservasi</p>
            </div>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <?= htmlspecialchars($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Room Details -->
            <div class="col-lg-5">
                <div class="card shadow-lg fade-in-up">
                    <img src="../assets/img/<?= strtolower(str_replace(' ', '', $room['tipe_kamar'])) ?>.jpg" 
                         class="card-img-top" alt="<?= htmlspecialchars($room['tipe_kamar']) ?>" 
                         style="height: 300px; object-fit: cover;">
                    <div class="card-body">
                        <h4 class="card-title text-gold fw-bold"><?= htmlspecialchars($room['tipe_kamar']); ?></h4>
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Nomor Kamar</small>
                                <p class="fw-semibold"><?= htmlspecialchars($room['nomor_kamar']); ?></p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Harga/Malam</small>
                                <p class="fw-semibold">Rp<?= number_format($room['harga_per_malam'], 0, ',', '.'); ?></p>
                            </div>
                        </div>
                        <div class="mb-3">
                            <small class="text-muted">Deskripsi</small>
                            <p class="text-muted"><?= nl2br(htmlspecialchars($room['deskripsi'])); ?></p>
                        </div>
                        <div class="total-display">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fs-5">Total Harga</span>
                                <span class="fs-3 fw-bold" id="total-price">Rp<?= number_format($room['harga_per_malam'], 0, ',', '.'); ?></span>
                            </div>
                            <small class="opacity-75">Per malam</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <div class="col-lg-7">
                <div class="card shadow-lg fade-in-up">
                    <div class="card-body">
                        <form action="proses_booking.php" method="post" id="bookingForm">
                            <input type="hidden" name="room_id" value="<?= $room['id']; ?>">
                            <input type="hidden" name="tipe_kamar" value="<?= htmlspecialchars($room['tipe_kamar']); ?>">
                            <input type="hidden" name="harga_per_malam" value="<?= $room['harga_per_malam']; ?>">
                            
                            <!-- Personal Information -->
                            <div class="mb-4">
                                <h5 class="text-gold fw-bold mb-3">
                                    <i class="bi bi-person-fill me-2"></i>Informasi Pribadi
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="nama" class="form-label">Nama Lengkap</label>
                                        <input type="text" name="nama" id="nama" class="form-control" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="no_hp" class="form-label">No HP</label>
                                        <input type="text" name="no_hp" id="no_hp" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <!-- Booking Details -->
                            <div class="mb-4">
                                <h5 class="text-gold fw-bold mb-3">
                                    <i class="bi bi-calendar-check-fill me-2"></i>Detail Booking
                                </h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="tanggal_checkin" class="form-label">Tanggal Check-in</label>
                                        <input type="date" name="tanggal_checkin" id="tanggal_checkin" class="form-control" required min="<?= date('Y-m-d') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="jumlah_malam" class="form-label">Jumlah Malam</label>
                                        <input type="number" name="jumlah_malam" id="jumlah_malam" class="form-control" required min="1" max="30" onchange="calculateTotal()">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="jumlah_tamu" class="form-label">Jumlah Tamu</label>
                                        <input type="number" name="jumlah_tamu" id="jumlah_tamu" class="form-control" required min="1" max="10">
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Method -->
                            <div class="mb-4">
                                <h5 class="text-gold fw-bold mb-3">
                                    <i class="bi bi-credit-card-fill me-2"></i>Metode Pembayaran
                                </h5>
                                <?php if (mysqli_num_rows($payment_methods) > 0): ?>
                                    <div class="row g-3">
                                        <?php while ($method = mysqli_fetch_assoc($payment_methods)): ?>
                                            <div class="col-md-6">
                                                <div class="payment-method" onclick="selectPayment(this)">
                                                    <input type="radio" name="metode_pembayaran_id" value="<?= $method['id'] ?>" required>
                                                    <div class="d-flex align-items-center">
                                                        <i class="bi bi-credit-card text-primary me-3" style="font-size: 1.5rem;"></i>
                                                        <div>
                                                            <h6 class="mb-1"><?= htmlspecialchars($method['nama_metode']) ?></h6>
                                                            <small class="text-muted"><?= htmlspecialchars($method['deskripsi']) ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endwhile; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="alert alert-warning">Tidak ada metode pembayaran yang tersedia saat ini.</div>
                                <?php endif; ?>
                            </div>

                            <!-- Additional Notes -->
                            <div class="mb-4">
                                <label for="catatan" class="form-label">Catatan Tambahan</label>
                                <textarea name="catatan" id="catatan" class="form-control" rows="3" 
                                          placeholder="Permintaan khusus atau catatan untuk hotel..."></textarea>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex gap-3">
                                <a href="kamar.php" class="btn btn-outline-secondary flex-fill">
                                    <i class="bi bi-arrow-left me-1"></i>Kembali
                                </a>
                                <button type="submit" class="btn btn-primary flex-fill">
                                    <i class="bi bi-check-circle me-1"></i>Konfirmasi Booking
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/user.js"></script>

<script>
function selectPayment(element) {
    // Remove selected class from all payment methods
    document.querySelectorAll('.payment-method').forEach(method => {
        method.classList.remove('selected');
    });
    
    // Add selected class to clicked element
    element.classList.add('selected');
    
    // Check the radio button
    element.querySelector('input[type="radio"]').checked = true;
}

function calculateTotal() {
    const nights = document.getElementById('jumlah_malam').value || 1;
    const pricePerNight = <?= $room['harga_per_malam'] ?>;
    const total = nights * pricePerNight;
    
    document.getElementById('total-price').textContent = 'Rp' + total.toLocaleString('id-ID');
}

// Set minimum date to today
document.getElementById('tanggal_checkin').min = new Date().toISOString().split('T')[0];
</script>
</body>
</html>
