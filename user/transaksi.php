<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

$booking_id = isset($_GET['booking_id']) ? (int)$_GET['booking_id'] : 0;

if ($booking_id == 0) {
    header("Location: dashboard.php");
    exit;
}

// Ambil data booking dengan pembayaran (dengan kolom bukti pembayaran)
$query = "SELECT b.*, p.metode_pembayaran_id, p.jumlah as jumlah_pembayaran, p.status as status_pembayaran, 
                 p.bukti_pembayaran, p.keterangan_pembayaran, p.tanggal_upload,
                 mp.nama_metode, mp.deskripsi as metode_deskripsi, mp.nomor_rekening, mp.atas_nama
          FROM booking b 
          LEFT JOIN pembayaran p ON b.id = p.booking_id 
          LEFT JOIN metode_pembayaran mp ON p.metode_pembayaran_id = mp.id 
          WHERE b.id = ? AND b.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$booking = $result->fetch_assoc();

if (!$booking) {
    header("Location: dashboard.php");
    exit;
}

// Hitung tanggal checkout
$checkin_date = new DateTime($booking['created_at']);
$checkout_date = clone $checkin_date;
$checkout_date->add(new DateInterval('P' . $booking['jumlah_malam'] . 'D'));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Transaksi - HotelEase</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .transaction-card {
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border-radius: 15px;
        }
        .status-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
        }
        .status-diajukan {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-dikonfirmasi {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .status-selesai {
            background-color: #d4edda;
            color: #155724;
        }
        .status-dibatalkan {
            background-color: #f8d7da;
            color: #721c24;
        }
        .payment-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
            padding: 20px;
        }
        .booking-summary {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
        }
        .price-breakdown {
            border-top: 2px dashed #dee2e6;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <!-- Hamburger panel will be injected by hamburger-universal.js -->
    <?php require('sidebar.php'); ?>

<!-- Main Content -->
<div class="main-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col">
                <div class="d-flex align-items-center">
                    <a href="dashboard.php" class="btn btn-outline-secondary me-3">
                        <i class="bi bi-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="fw-bold text-gold mb-2 fade-in-up">Detail Transaksi</h2>
                        <p class="text-muted fade-in-up">Booking ID: #<?= $booking['id'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <span class="status-badge status-<?= $booking['status'] ?>">
                    <?= ucfirst(str_replace('_', ' ', $booking['status'])) ?>
                </span>
            </div>
        </div>

        <div class="row g-4">
            <!-- Booking Summary -->
            <div class="col-lg-8">
                <div class="card transaction-card fade-in-up">
                    <div class="card-body">
                        <h5 class="card-title text-gold fw-bold mb-4">
                            <i class="bi bi-calendar-check me-2"></i>Ringkasan Booking
                        </h5>
                        
                        <div class="booking-summary">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <small class="text-muted">Nama Tamu</small>
                                        <h6 class="fw-semibold"><?= htmlspecialchars($booking['nama']) ?></h6>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">No. HP</small>
                                        <h6 class="fw-semibold"><?= htmlspecialchars($booking['no_hp']) ?></h6>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">Tipe Kamar</small>
                                        <h6 class="fw-semibold"><?= htmlspecialchars($booking['tipe_kamar']) ?></h6>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <small class="text-muted">Jumlah Tamu</small>
                                        <h6 class="fw-semibold"><?= $booking['jumlah_tamu'] ?> orang</h6>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">Durasi Menginap</small>
                                        <h6 class="fw-semibold"><?= $booking['jumlah_malam'] ?> malam</h6>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">Tanggal Booking</small>
                                        <h6 class="fw-semibold"><?= date('d F Y', strtotime($booking['created_at'])) ?></h6>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if ($booking['catatan']): ?>
                            <div class="mt-3 pt-3 border-top">
                                <small class="text-muted">Catatan Tambahan</small>
                                <p class="mb-0"><?= nl2br(htmlspecialchars($booking['catatan'])) ?></p>
                            </div>
                            <?php endif; ?>
                        </div>

                        <!-- Price Breakdown -->
                        <div class="price-breakdown mt-4">
                            <div class="row">
                                <div class="col-8">
                                    <h6 class="fw-semibold">Total Pembayaran</h6>
                                </div>
                                <div class="col-4 text-end">
                                    <h4 class="fw-bold text-gold">Rp<?= number_format($booking['harga_total'], 0, ',', '.') ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="col-lg-4">
                <div class="card transaction-card fade-in-up">
                    <div class="card-body p-0">
                        <div class="payment-info">
                            <h5 class="fw-bold mb-3">
                                <i class="bi bi-credit-card me-2"></i>Informasi Pembayaran
                            </h5>
                            
                            <?php if ($booking['nama_metode']): ?>
                            <div class="mb-3">
                                <small class="opacity-75">Metode Pembayaran</small>
                                <h6 class="fw-semibold"><?= htmlspecialchars($booking['nama_metode']) ?></h6>
                                <?php if ($booking['metode_deskripsi']): ?>
                                <small class="opacity-75"><?= htmlspecialchars($booking['metode_deskripsi']) ?></small>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($booking['nomor_rekening']): ?>
                            <div class="mb-3">
                                <small class="opacity-75">Nomor Rekening</small>
                                <h6 class="fw-semibold font-monospace"><?= htmlspecialchars($booking['nomor_rekening']) ?></h6>
                                <?php if ($booking['atas_nama']): ?>
                                <small class="opacity-75">a.n. <?= htmlspecialchars($booking['atas_nama']) ?></small>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <small class="opacity-75">Status Pembayaran</small>
                                <h6 class="fw-semibold">
                                    <?php 
                                    $status_display = [
                                        'belum_dibayar' => 'Belum Dibayar',
                                        'menunggu_verifikasi' => 'Menunggu Verifikasi',
                                        'lunas' => 'Lunas',
                                        'gagal' => 'Gagal'
                                    ];
                                    echo $status_display[$booking['status_pembayaran']] ?? 'Tidak Diketahui';
                                    ?>
                                </h6>
                            </div>
                            
                            <div>
                                <small class="opacity-75">Jumlah</small>
                                <h5 class="fw-bold">Rp<?= number_format($booking['jumlah_pembayaran'], 0, ',', '.') ?></h5>
                            </div>
                            <?php else: ?>
                            <div class="text-center">
                                <i class="bi bi-exclamation-circle" style="font-size: 3rem; opacity: 0.5;"></i>
                                <p class="mt-2 opacity-75">Informasi pembayaran tidak tersedia</p>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="p-3">
                            <?php if ($booking['status_pembayaran'] == 'belum_dibayar'): ?>
                            <button class="btn btn-success w-100 mb-2" onclick="uploadPaymentProof()">
                                <i class="bi bi-upload me-2"></i>Upload Bukti Pembayaran
                            </button>
                            <?php endif; ?>
                            
                            <button class="btn btn-outline-primary w-100 mb-2" onclick="printReceipt()">
                                <i class="bi bi-printer me-2"></i>Cetak Bukti Booking
                            </button>
                            
                            <?php if ($booking['status'] == 'diajukan'): ?>
                            <button class="btn btn-outline-danger w-100" onclick="cancelBooking()">
                                <i class="bi bi-x-circle me-2"></i>Batalkan Booking
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Timeline -->
                <div class="card transaction-card mt-4 fade-in-up">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">Timeline Booking</h6>
                        <div class="timeline">
                            <div class="timeline-item completed">
                                <i class="bi bi-check-circle-fill"></i>
                                <div>
                                    <small class="text-muted">Booking Dibuat</small>
                                    <p class="mb-0 fw-semibold"><?= date('d M Y, H:i', strtotime($booking['created_at'])) ?></p>
                                </div>
                            </div>
                            
                            <?php if ($booking['status'] != 'diajukan'): ?>
                            <div class="timeline-item <?= in_array($booking['status'], ['dikonfirmasi', 'selesai']) ? 'completed' : '' ?>">
                                <i class="bi bi-check-circle-fill"></i>
                                <div>
                                    <small class="text-muted">Booking Dikonfirmasi</small>
                                    <p class="mb-0 fw-semibold">-</p>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if ($booking['status'] == 'selesai'): ?>
                            <div class="timeline-item completed">
                                <i class="bi bi-check-circle-fill"></i>
                                <div>
                                    <small class="text-muted">Check-out Selesai</small>
                                    <p class="mb-0 fw-semibold">-</p>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Bukti Pembayaran -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Upload Bukti Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="uploadForm" enctype="multipart/form-data">
                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                    <div class="mb-3">
                        <label for="bukti_pembayaran" class="form-label">File Bukti Pembayaran</label>
                        <input type="file" class="form-control" name="bukti_pembayaran" id="bukti_pembayaran" 
                               accept="image/*,.pdf" required>
                        <small class="text-muted">Format: JPG, PNG, GIF, WEBP atau PDF. Maksimal 10MB<br>
                        <em>Untuk keperluan pembelajaran, Anda dapat menggunakan gambar/foto apapun sebagai bukti pembayaran.</em></small>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" name="keterangan" rows="3" 
                                  placeholder="Tambahkan keterangan jika diperlukan..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" onclick="submitUpload()">
                    <i class="bi bi-upload me-1"></i>Upload
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/user.js"></script>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
    opacity: 0.5;
}

.timeline-item.completed {
    opacity: 1;
}

.timeline-item:not(:last-child):before {
    content: '';
    position: absolute;
    left: -22px;
    top: 25px;
    height: calc(100% - 10px);
    width: 2px;
    background: #dee2e6;
}

.timeline-item.completed:not(:last-child):before {
    background: #28a745;
}

.timeline-item i {
    position: absolute;
    left: -30px;
    top: 2px;
    color: #dee2e6;
}

.timeline-item.completed i {
    color: #28a745;
}
</style>

<script>
function uploadPaymentProof() {
    $('#uploadModal').modal('show');
}

function submitUpload() {
    const form = document.getElementById('uploadForm');
    const formData = new FormData(form);

    fetch('upload_bukti.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Gagal mengupload bukti pembayaran.');
    });
}

function printReceipt() {
    window.print();
}

function cancelBooking() {
    if (confirm('Apakah Anda yakin ingin membatalkan booking ini?')) {
        // Implement cancel booking logic
        alert('Fitur batalkan booking akan diimplementasikan');
    }
}
</script>

</body>
</html>
