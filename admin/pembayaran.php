<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Handle Verifikasi Pembayaran
if (isset($_POST['verify_payment'])) {
    $payment_id = $_POST['payment_id'];
    $status = $_POST['status'];
    $verified_by = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("UPDATE pembayaran SET status = ?, verified_by = ?, verified_at = NOW() WHERE id = ?");
    $stmt->bind_param("sii", $status, $verified_by, $payment_id);
    $stmt->execute();
    
    // Update booking status jika pembayaran diverifikasi
    if ($status == 'dibayar') {
        $booking_query = "SELECT booking_id FROM pembayaran WHERE id = ?";
        $booking_stmt = $conn->prepare($booking_query);
        $booking_stmt->bind_param("i", $payment_id);
        $booking_stmt->execute();
        $booking_result = $booking_stmt->get_result();
        $booking_data = $booking_result->fetch_assoc();
        
        if ($booking_data) {
            $update_booking = $conn->prepare("UPDATE booking SET status = 'diterima' WHERE id = ?");
            $update_booking->bind_param("i", $booking_data['booking_id']);
            $update_booking->execute();
        }
    }
    
    header("Location: pembayaran.php");
    exit;
}

// Get all payments with booking and user info
$query = "SELECT p.*, b.nama, b.tipe_kamar, b.harga_total, u.nama_lengkap as user_name, 
                mp.nama_metode, v.nama_lengkap as verified_by_name
            FROM pembayaran p 
            LEFT JOIN booking b ON p.booking_id = b.id 
            LEFT JOIN users u ON b.user_id = u.id 
            LEFT JOIN metode_pembayaran mp ON p.metode_pembayaran_id = mp.id
            LEFT JOIN users v ON p.verified_by = v.id
            ORDER BY p.tanggal_pembayaran DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pembayaran - HotelEase</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="admin-body">
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

    <div class="main-content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
                <div>
                    <h2 class="fw-bold text-gold mb-1">
                        <i class="bi bi-credit-card me-2"></i>Manajemen Pembayaran
                    </h2>
                    <p class="text-muted mb-0">Kelola dan verifikasi pembayaran tamu</p>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4 fade-in-up">
                <?php
                mysqli_data_seek($result, 0);
                $total_payments = 0;
                $pending_count = 0;
                $verified_count = 0;
                $total_amount = 0;
                
                while ($row = mysqli_fetch_assoc($result)) {
                    $total_payments++;
                    $total_amount += $row['jumlah'];
                    if ($row['status'] == 'menunggu_verifikasi') $pending_count++;
                    if ($row['status'] == 'dibayar') $verified_count++;
                }
                mysqli_data_seek($result, 0);
                ?>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                                    <i class="bi bi-receipt text-primary fs-4"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold text-primary mb-1"><?= $total_payments ?></h3>
                            <p class="text-muted mb-0">Total Pembayaran</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <div class="rounded-circle bg-warning bg-opacity-10 p-3">
                                    <i class="bi bi-clock text-warning fs-4"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold text-warning mb-1"><?= $pending_count ?></h3>
                            <p class="text-muted mb-0">Menunggu Verifikasi</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <div class="rounded-circle bg-success bg-opacity-10 p-3">
                                    <i class="bi bi-check-circle text-success fs-4"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold text-success mb-1"><?= $verified_count ?></h3>
                            <p class="text-muted mb-0">Terverifikasi</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="d-flex align-items-center justify-content-center mb-3">
                                <div class="rounded-circle bg-info bg-opacity-10 p-3">
                                    <i class="bi bi-currency-dollar text-info fs-4"></i>
                                </div>
                            </div>
                            <h3 class="fw-bold text-info mb-1">Rp <?= number_format($total_amount, 0, ',', '.') ?></h3>
                            <p class="text-muted mb-0">Total Nilai</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Table -->
            <div class="card border-0 shadow-sm fade-in-up">
                <div class="card-header bg-white border-bottom-0 py-3">
                    <h5 class="card-title mb-0 text-gold">
                        <i class="bi bi-list-ul me-2"></i>Daftar Pembayaran
                    </h5>
                </div>
                <div class="card-body p-0">
                    <?php if (mysqli_num_rows($result) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="border-0 ps-4">ID</th>
                                    <th class="border-0">Booking</th>
                                    <th class="border-0">Tamu</th>
                                    <th class="border-0">Metode</th>
                                    <th class="border-0">Jumlah</th>
                                    <th class="border-0">Bukti</th>
                                    <th class="border-0">Status</th>
                                    <th class="border-0">Tanggal</th>
                                    <th class="border-0 pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr class="border-bottom">
                                    <td class="ps-4 fw-bold"><?= $row['id'] ?></td>
                                    <td>
                                        <div>
                                            <span class="fw-semibold"><?= htmlspecialchars($row['nama']) ?></span>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-house me-1"></i><?= htmlspecialchars($row['tipe_kamar']) ?>
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-circle me-2 text-primary"></i>
                                            <span><?= htmlspecialchars($row['user_name']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-credit-card me-2 text-info"></i>
                                            <span><?= htmlspecialchars($row['nama_metode']) ?></span>
                                        </div>
                                    </td>
                                    <td class="fw-bold text-success">Rp <?= number_format($row['jumlah'], 0, ',', '.') ?></td>
                                    <td>
                                        <?php if ($row['bukti_pembayaran']): ?>
                                            <a href="../uploads/bukti/<?= $row['bukti_pembayaran'] ?>" target="_blank" 
                                               class="btn btn-sm btn-outline-info">
                                                <i class="bi bi-eye me-1"></i>Lihat
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">
                                                <i class="bi bi-dash-circle me-1"></i>Tidak ada
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = '';
                                        $icon = '';
                                        switch($row['status']) {
                                            case 'belum_dibayar': 
                                                $badge_class = 'bg-secondary'; 
                                                $icon = 'clock';
                                                break;
                                            case 'menunggu_verifikasi': 
                                                $badge_class = 'bg-warning'; 
                                                $icon = 'hourglass-split';
                                                break;
                                            case 'dibayar': 
                                                $badge_class = 'bg-success'; 
                                                $icon = 'check-circle';
                                                break;
                                            case 'gagal': 
                                                $badge_class = 'bg-danger'; 
                                                $icon = 'x-circle';
                                                break;
                                        }
                                        ?>
                                        <span class="badge <?= $badge_class ?> px-3 py-2">
                                            <i class="bi bi-<?= $icon ?> me-1"></i>
                                            <?= ucwords(str_replace('_', ' ', $row['status'])) ?>
                                        </span>
                                    </td>
                                    <td class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        <?= date('d/m/Y H:i', strtotime($row['tanggal_pembayaran'])) ?>
                                    </td>
                                    <td class="pe-4">
                                        <?php if ($row['status'] == 'menunggu_verifikasi'): ?>
                                            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" 
                                                    data-bs-target="#verifyModal" 
                                                    data-id="<?= $row['id'] ?>"
                                                    data-nama="<?= htmlspecialchars($row['nama']) ?>"
                                                    title="Verifikasi Pembayaran">
                                                <i class="bi bi-check-circle me-1"></i>Verifikasi
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-receipt text-muted" style="font-size: 4rem;"></i>
                        <h5 class="text-muted mt-3">Belum ada pembayaran</h5>
                        <p class="text-muted">Pembayaran akan muncul di sini setelah tamu melakukan booking</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Verify Modal -->
    <div class="modal fade" id="verifyModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-check-circle me-2"></i>Verifikasi Pembayaran
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="payment_id" id="payment_id">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            Verifikasi pembayaran untuk booking: <strong id="booking_nama"></strong>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status Pembayaran</label>
                            <select class="form-control" name="status" required>
                                <option value="">Pilih status verifikasi</option>
                                <option value="dibayar">✓ Dibayar (Terverifikasi)</option>
                                <option value="gagal">✗ Gagal/Ditolak</option>
                            </select>
                        </div>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <strong>Perhatian:</strong> Pastikan Anda telah memeriksa bukti pembayaran dengan teliti sebelum melakukan verifikasi.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Batal
                        </button>
                        <button type="submit" name="verify_payment" class="btn btn-success">
                            <i class="bi bi-check-circle me-1"></i>Verifikasi Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/admin.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const verifyModal = document.getElementById('verifyModal');
        verifyModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const paymentId = button.getAttribute('data-id');
            const bookingNama = button.getAttribute('data-nama');
            
            document.getElementById('payment_id').value = paymentId;
            document.getElementById('booking_nama').textContent = bookingNama;
        });
    });
    </script>
</body>
</html>