<?php
session_start();
require '../config/koneksi.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Handle Export
if (isset($_GET['export'])) {
    $type = $_GET['export'];
    $start_date = $_GET['start_date'] ?? date('Y-m-01');
    $end_date = $_GET['end_date'] ?? date('Y-m-d');
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="laporan_' . $type . '_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    if ($type == 'booking') {
        fputcsv($output, ['ID', 'Nama', 'No HP', 'Tipe Kamar', 'Jumlah Tamu', 'Jumlah Malam', 'Harga Total', 'Status', 'Tanggal']);
        
        $query = "SELECT * FROM booking WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date' ORDER BY created_at DESC";
        $result = mysqli_query($conn, $query);
        
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, [
                $row['id'],
                $row['nama'],
                $row['no_hp'],
                $row['tipe_kamar'],
                $row['jumlah_tamu'],
                $row['jumlah_malam'],
                $row['harga_total'],
                $row['status'],
                $row['created_at']
            ]);
        }
    } elseif ($type == 'pembayaran') {
        fputcsv($output, ['ID', 'Booking ID', 'Metode', 'Jumlah', 'Status', 'Tanggal']);
        
        $query = "SELECT p.*, mp.nama_metode FROM pembayaran p 
                  LEFT JOIN metode_pembayaran mp ON p.metode_pembayaran_id = mp.id
                  WHERE DATE(p.tanggal_pembayaran) BETWEEN '$start_date' AND '$end_date' 
                  ORDER BY p.tanggal_pembayaran DESC";
        $result = mysqli_query($conn, $query);
        
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($output, [
                $row['id'],
                $row['booking_id'],
                $row['nama_metode'],
                $row['jumlah'],
                $row['status'],
                $row['tanggal_pembayaran']
            ]);
        }
    }
    
    fclose($output);
    exit;
}

// Filter dates
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Statistics
$qPendapatan = mysqli_query($conn, "SELECT SUM(harga_total) AS total_pendapatan FROM booking WHERE status = 'diterima' AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'");
$pendapatan = mysqli_fetch_assoc($qPendapatan)['total_pendapatan'] ?? 0;

$qPengunjung = mysqli_query($conn, "SELECT COUNT(*) AS total_pengunjung FROM booking WHERE status = 'diterima' AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'");
$pengunjung = mysqli_fetch_assoc($qPengunjung)['total_pengunjung'] ?? 0;

$qBookingTotal = mysqli_query($conn, "SELECT COUNT(*) AS total_booking FROM booking WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'");
$total_booking = mysqli_fetch_assoc($qBookingTotal)['total_booking'] ?? 0;

$qPembayaran = mysqli_query($conn, "SELECT COUNT(*) AS total_pembayaran, SUM(jumlah) AS total_dibayar FROM pembayaran WHERE status = 'dibayar' AND DATE(tanggal_pembayaran) BETWEEN '$start_date' AND '$end_date'");
$pembayaran_data = mysqli_fetch_assoc($qPembayaran);

// Monthly data for chart
$monthly_query = "SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    SUM(harga_total) as revenue,
    COUNT(*) as bookings
    FROM booking 
    WHERE status = 'diterima' AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month";
$monthly_result = mysqli_query($conn, $monthly_query);
$monthly_data = [];
while ($row = mysqli_fetch_assoc($monthly_result)) {
    $monthly_data[] = $row;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan & Analytics - HotelEase</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Chart.js untuk membuat grafik -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    <!-- Main Content -->
    <main class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
                <h2 class="fw-bold text-gold">
                    <i class="bi bi-graph-up me-2"></i>Laporan & Analytics
                </h2>
                <a href="export_pdf.php?start_date=<?= $start_date ?>&end_date=<?= $end_date ?>" class="btn btn-primary shadow-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Export Laporan PDF
                </a>
            </div>

            <!-- Date Filter -->
            <div class="card shadow-lg mb-4 fade-in-up">
                <div class="card-header bg-gradient-primary text-black">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel me-2"></i>Filter Periode Laporan
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-calendar-date me-1"></i>Tanggal Mulai
                            </label>
                            <input type="date" class="form-control" name="start_date" value="<?= $start_date ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">
                                <i class="bi bi-calendar-date me-1"></i>Tanggal Akhir
                            </label>
                            <input type="date" class="form-control" name="end_date" value="<?= $end_date ?>">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary d-block w-100">
                                <i class="bi bi-search me-1"></i>Filter Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card stats-card bg-gradient-success text-black shadow-lg fade-in-up">
                        <div class="card-body text-center">
                            <i class="bi bi-currency-dollar fs-1 mb-2"></i>
                            <h5 class="card-title">Total Pendapatan</h5>
                            <h3 class="fw-bold">Rp <?= number_format($pendapatan, 0, ',', '.') ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card bg-gradient-primary text-black shadow-lg fade-in-up">
                        <div class="card-body text-center">
                            <i class="bi bi-people fs-1 mb-2"></i>
                            <h5 class="card-title">Pengunjung</h5>
                            <h3 class="fw-bold"><?= $pengunjung ?> orang</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card bg-gradient-info text-black shadow-lg fade-in-up">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar-check fs-1 mb-2"></i>
                            <h5 class="card-title">Total Booking</h5>
                            <h3 class="fw-bold"><?= $total_booking ?></h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card stats-card bg-gradient-warning text-black shadow-lg fade-in-up">
                        <div class="card-body text-center">
                            <i class="bi bi-credit-card fs-1 mb-2"></i>
                            <h5 class="card-title">Pembayaran</h5>
                            <h3 class="fw-bold"><?= $pembayaran_data['total_pembayaran'] ?? 0 ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts -->
            <div class="row">
                <div class="col-md-8 mb-4">
                    <div class="card shadow-lg fade-in-up">
                        <div class="card-header bg-gradient-info text-black">
                            <h5 class="mb-0">
                                <i class="bi bi-graph-up me-2"></i>Pendapatan Bulanan (12 Bulan Terakhir)
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="revenueChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card shadow-lg fade-in-up">
                        <div class="card-header bg-gradient-secondary text-black">
                            <h5 class="mb-0">
                                <i class="bi bi-pie-chart me-2"></i>Status Booking
                            </h5>
                        </div>
                        <div class="card-body">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/admin.js"></script>
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const monthlyData = <?= json_encode($monthly_data) ?>;

new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: monthlyData.map(item => item.month),
        datasets: [{
            label: 'Pendapatan (Rp)',
            data: monthlyData.map(item => item.revenue),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString();
                    }
                }
            }
        }
    }
});

// Status Chart
<?php
$status_query = "SELECT status, COUNT(*) as count FROM booking WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date' GROUP BY status";
$status_result = mysqli_query($conn, $status_query);
$status_data = [];
while ($row = mysqli_fetch_assoc($status_result)) {
    $status_data[] = $row;
}
?>

const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusData = <?= json_encode($status_data) ?>;

new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: statusData.map(item => item.status),
        datasets: [{
            data: statusData.map(item => item.count),
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false
    }
});
</script>
</body>
</html>
