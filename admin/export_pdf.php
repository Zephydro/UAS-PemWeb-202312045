<?php
session_start();
require '../config/koneksi.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Filter dates
$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date = $_GET['end_date'] ?? date('Y-m-d');

// Collect all data for the report
// Statistics
$qPendapatan = mysqli_query($conn, "SELECT SUM(harga_total) AS total_pendapatan FROM booking WHERE status = 'diterima' AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'");
$pendapatan = mysqli_fetch_assoc($qPendapatan)['total_pendapatan'] ?? 0;

$qPengunjung = mysqli_query($conn, "SELECT COUNT(*) AS total_pengunjung FROM booking WHERE status = 'diterima' AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'");
$pengunjung = mysqli_fetch_assoc($qPengunjung)['total_pengunjung'] ?? 0;

$qBookingTotal = mysqli_query($conn, "SELECT COUNT(*) AS total_booking FROM booking WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date'");
$total_booking = mysqli_fetch_assoc($qBookingTotal)['total_booking'] ?? 0;

$qPembayaran = mysqli_query($conn, "SELECT COUNT(*) AS total_pembayaran, SUM(jumlah) AS total_dibayar FROM pembayaran WHERE status = 'dibayar' AND DATE(tanggal_pembayaran) BETWEEN '$start_date' AND '$end_date'");
$pembayaran_data = mysqli_fetch_assoc($qPembayaran);

// Payment method statistics
$qMetodeTerpopuler = mysqli_query($conn, "
    SELECT mp.nama_metode, COUNT(p.id) as jumlah_transaksi, SUM(p.jumlah) as total_nilai
    FROM pembayaran p 
    LEFT JOIN metode_pembayaran mp ON p.metode_pembayaran_id = mp.id
    WHERE p.status = 'dibayar' AND DATE(p.tanggal_pembayaran) BETWEEN '$start_date' AND '$end_date'
    GROUP BY p.metode_pembayaran_id
    ORDER BY jumlah_transaksi DESC
");
$metode_pembayaran_stats = [];
while ($row = mysqli_fetch_assoc($qMetodeTerpopuler)) {
    $metode_pembayaran_stats[] = $row;
}

// Booking status breakdown
$qStatusBooking = mysqli_query($conn, "
    SELECT status, COUNT(*) as jumlah 
    FROM booking 
    WHERE DATE(created_at) BETWEEN '$start_date' AND '$end_date' 
    GROUP BY status
");
$status_booking = [];
while ($row = mysqli_fetch_assoc($qStatusBooking)) {
    $status_booking[] = $row;
}

// Monthly revenue data (last 12 months)
$qMonthlyRevenue = mysqli_query($conn, "
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as bulan,
        SUM(harga_total) as pendapatan,
        COUNT(*) as jumlah_booking
    FROM booking 
    WHERE status = 'diterima' AND created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY bulan DESC
    LIMIT 12
");
$monthly_revenue = [];
while ($row = mysqli_fetch_assoc($qMonthlyRevenue)) {
    $monthly_revenue[] = $row;
}

// Recent bookings
$qRecentBookings = mysqli_query($conn, "
    SELECT b.*, mp.nama_metode, p.status as payment_status, p.tanggal_pembayaran
    FROM booking b
    LEFT JOIN pembayaran p ON b.id = p.booking_id
    LEFT JOIN metode_pembayaran mp ON p.metode_pembayaran_id = mp.id
    WHERE DATE(b.created_at) BETWEEN '$start_date' AND '$end_date'
    ORDER BY b.created_at DESC
    LIMIT 15
");
$recent_bookings = [];
while ($row = mysqli_fetch_assoc($qRecentBookings)) {
    $recent_bookings[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Komprehensif HotelEase</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @media print {
            .no-print { display: none !important; }
            @page { margin: 1.5cm; size: A4; }
            body { -webkit-print-color-adjust: exact; color-adjust: exact; }
        }

        body {
            font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.7;
            color: #2c3e50;
            background: linear-gradient(135deg, #f8fafe 0%, #f1f4f8 100%);
            min-height: 100vh;
            font-weight: 400;
        }

        @media print {
            body { background: white !important; }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
            background: #fdfaefff;
            box-shadow: 0 4px 25px rgba(0,0,0,0.05);
            border-radius: 16px;
            margin-top: 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(212, 175, 55, 0.1);
        }

        @media print {
            .container {
                box-shadow: none;
                border-radius: 0;
                margin: 0;
                padding: 0;
                border: none;
            }
        }

        .header {
            text-align: center;
            border-bottom: 2px solid rgba(212, 175, 55, 0.2);
            padding-bottom: 30px;
            margin-bottom: 40px;
            position: relative;
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 2px;
            background: linear-gradient(90deg, #d4af37, #f4e19c);
        }

        .header h1 {
            color: #2c3e50;
            font-size: 2.8rem;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .header h1::before {
            content: '✦';
            color: #d4af37;
            margin-right: 12px;
            font-size: 0.8em;
        }

        .header h1::after {
            content: '✦';
            color: #d4af37;
            margin-left: 12px;
            font-size: 0.8em;
        }

        .header h2 {
            color: #64748b;
            font-size: 1.6rem;
            font-weight: 400;
            margin-bottom: 20px;
            letter-spacing: 0.5px;
        }

        .header p {
            color: #7c8898;
            font-size: 0.95rem;
            margin: 4px 0;
            font-weight: 400;
        }

        .print-btn {
            position: fixed;
            top: 30px;
            right: 30px;
            background: linear-gradient(135deg, #d4af37 0%, #f4e19c 100%);
            color: #2c3e50;
            border: none;
            padding: 14px 28px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.25);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            z-index: 1000;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(212, 175, 55, 0.3);
        }

        .print-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(212, 175, 55, 0.35);
            background: linear-gradient(135deg, #f4e19c 0%, #d4af37 100%);
        }

        .summary-box {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafe 100%);
            border: 1px solid rgba(212, 175, 55, 0.15);
            padding: 35px;
            border-radius: 20px;
            margin-bottom: 40px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.04);
            position: relative;
            overflow: hidden;
        }

        .summary-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #d4af37, #f4e19c, #d4af37);
        }

        .summary-title {
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 30px;
            text-align: center;
            color: #2c3e50;
            letter-spacing: 0.5px;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
        }

        .summary-item {
            text-align: center;
            background: #ffffff;
            padding: 25px 20px;
            border-radius: 16px;
            border: 1px solid rgba(212, 175, 55, 0.1);
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            transition: all 0.3s ease;
        }

        .summary-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            border-color: rgba(212, 175, 55, 0.2);
        }

        .summary-item h3 {
            font-size: 0.9rem;
            margin-bottom: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 500;
        }

        .summary-item .value {
            font-size: 1.9rem;
            font-weight: 700;
            color: #d4af37;
            text-shadow: 0 1px 2px rgba(212, 175, 55, 0.1);
        }

        .section {
            margin-bottom: 45px;
            background: #ffffff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            border: 1px solid rgba(212, 175, 55, 0.08);
            position: relative;
        }

        .section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, #d4af37, #f4e19c);
            border-radius: 0 0 0 16px;
        }

        .section-title {
            color: #2c3e50;
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 12px;
            border-bottom: 1px solid rgba(212, 175, 55, 0.15);
            letter-spacing: 0.3px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0,0,0,0.03);
            border: 1px solid rgba(212, 175, 55, 0.1);
        }

        .table th {
            background: linear-gradient(135deg, #f8fafe 0%, #f1f4f8 100%);
            color: #2c3e50;
            padding: 18px 16px;
            text-align: left;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            border-bottom: 2px solid rgba(212, 175, 55, 0.1);
        }

        .table td {
            padding: 16px;
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            font-size: 0.95rem;
            color: #475569;
        }

        .table tr:hover {
            background-color: rgba(212, 175, 55, 0.02);
        }

        .table tr:last-child td {
            border-bottom: none;
        }

        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .status-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafe 100%);
            padding: 25px 20px;
            border-radius: 16px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            border: 1px solid rgba(212, 175, 55, 0.1);
            transition: all 0.3s ease;
        }

        .status-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
        }

        .status-card h4 {
            color: #64748b;
            font-size: 0.9rem;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 500;
        }

        .status-card .count {
            font-size: 1.8rem;
            font-weight: 700;
            color: #d4af37;
            text-shadow: 0 1px 2px rgba(212, 175, 55, 0.1);
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid rgba(212, 175, 55, 0.15);
            color: #7c8898;
            font-style: italic;
            font-size: 0.9rem;
            line-height: 1.8;
        }

        .highlight {
            background: linear-gradient(120deg, rgba(212, 175, 55, 0.1) 0%, rgba(244, 225, 156, 0.15) 100%);
            padding: 4px 12px;
            border-radius: 8px;
            color: #2c3e50;
            font-weight: 600;
            border: 1px solid rgba(212, 175, 55, 0.15);
        }

        .currency {
            color: #059669;
            font-weight: 600;
        }

        .page-break {
            page-break-before: always;
        }

        @media print {
            .page-break {
                page-break-before: always;
            }
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                padding: 20px;
                border-radius: 12px;
            }
            
            .header h1 {
                font-size: 2.2rem;
            }
            
            .summary-grid {
                grid-template-columns: 1fr;
            }
            
            .status-grid {
                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            }
            
            .table {
                font-size: 0.85rem;
            }
            
            .table th,
            .table td {
                padding: 12px 8px;
            }
        }

        /* Enhanced visual hierarchy */
        .metric-icon {
            font-size: 1.2em;
            margin-right: 8px;
            opacity: 0.8;
        }

        .section-content {
            margin-top: 20px;
        }

        /* Subtle animations */
        .summary-item,
        .status-card,
        .section {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        🖨️ Cetak / Simpan PDF
    </button>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>HotelEase</h1>
            <h2>Laporan Komprehensif</h2>
            <p><strong>Periode:</strong> <?= date('d/m/Y', strtotime($start_date)) ?> - <?= date('d/m/Y', strtotime($end_date)) ?></p>
            <p><strong>Digenerate pada:</strong> <?= date('d/m/Y H:i:s') ?> WIB</p>
        </div>

        <!-- Executive Summary -->
        <div class="summary-box">
            <div class="summary-title">📊 Ringkasan Eksekutif</div>
            <div class="summary-grid">
                <div class="summary-item">
                    <h3><span class="metric-icon">💰</span>Total Pendapatan</h3>
                    <div class="value">Rp <?= number_format($pendapatan, 0, ',', '.') ?></div>
                </div>
                <div class="summary-item">
                    <h3><span class="metric-icon">👥</span>Total Pengunjung</h3>
                    <div class="value"><?= number_format($pengunjung) ?> orang</div>
                </div>
                <div class="summary-item">
                    <h3><span class="metric-icon">📅</span>Total Booking</h3>
                    <div class="value"><?= number_format($total_booking) ?></div>
                </div>
                <div class="summary-item">
                    <h3><span class="metric-icon">💳</span>Total Pembayaran</h3>
                    <div class="value"><?= number_format($pembayaran_data['total_pembayaran'] ?? 0) ?></div>
                </div>
            </div>
        </div>

        <!-- Payment Method Statistics -->
        <?php if (!empty($metode_pembayaran_stats)): ?>
        <div class="section">
            <h3 class="section-title">💳 Statistik Metode Pembayaran</h3>
            <div class="section-content">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Metode Pembayaran</th>
                            <th>Jumlah Transaksi</th>
                            <th>Total Nilai</th>
                            <th>Persentase</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_transaksi = array_sum(array_column($metode_pembayaran_stats, 'jumlah_transaksi'));
                        foreach ($metode_pembayaran_stats as $metode): 
                            $persentase = $total_transaksi > 0 ? ($metode['jumlah_transaksi'] / $total_transaksi * 100) : 0;
                        ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($metode['nama_metode'] ?? 'Tidak Diketahui') ?></strong></td>
                            <td><?= number_format($metode['jumlah_transaksi']) ?> transaksi</td>
                            <td class="currency">Rp <?= number_format($metode['total_nilai'], 0, ',', '.') ?></td>
                            <td><span class="highlight"><?= number_format($persentase, 1) ?>%</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Booking Status -->
        <?php if (!empty($status_booking)): ?>
        <div class="section">
            <h3 class="section-title">📈 Status Booking</h3>
            <div class="status-grid">
                <?php foreach ($status_booking as $status): ?>
                <div class="status-card">
                    <h4><?= ucfirst($status['status']) ?></h4>
                    <div class="count"><?= number_format($status['jumlah']) ?></div>
                    <small style="color: #7c8898;">booking</small>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Monthly Revenue -->
        <?php if (!empty($monthly_revenue)): ?>
        <div class="section page-break">
            <h3 class="section-title">📊 Tren Pendapatan Bulanan (12 Bulan Terakhir)</h3>
            <div class="section-content">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Pendapatan</th>
                            <th>Jumlah Booking</th>
                            <th>Rata-rata per Booking</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_reverse($monthly_revenue) as $month): 
                            $avg_per_booking = $month['jumlah_booking'] > 0 ? $month['pendapatan'] / $month['jumlah_booking'] : 0;
                        ?>
                        <tr>
                            <td><strong><?= date('M Y', strtotime($month['bulan'] . '-01')) ?></strong></td>
                            <td class="currency">Rp <?= number_format($month['pendapatan'], 0, ',', '.') ?></td>
                            <td><?= number_format($month['jumlah_booking']) ?> booking</td>
                            <td class="currency">Rp <?= number_format($avg_per_booking, 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Recent Bookings -->
        <?php if (!empty($recent_bookings)): ?>
        <div class="section">
            <h3 class="section-title">🏷️ Detail Booking Terbaru</h3>
            <div class="section-content">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Tipe Kamar</th>
                            <th>Tamu</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_bookings as $booking): ?>
                        <tr>
                            <td><strong>#<?= $booking['id'] ?></strong></td>
                            <td><?= htmlspecialchars($booking['nama']) ?></td>
                            <td><?= htmlspecialchars($booking['tipe_kamar']) ?></td>
                            <td><?= $booking['jumlah_tamu'] ?> orang</td>
                            <td class="currency">Rp <?= number_format($booking['harga_total'], 0, ',', '.') ?></td>
                            <td><span class="highlight"><?= ucfirst($booking['status']) ?></span></td>
                            <td><?= ucfirst($booking['payment_status'] ?? 'Belum Ada') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Footer -->
        <div class="footer">
            <p>📋 Laporan ini digenerate secara otomatis oleh sistem HotelEase</p>
            <p>📞 Untuk informasi lebih lanjut, hubungi administrator sistem</p>
            <p><strong>HotelEase Management System</strong> - Menghadirkan kemudahan dalam pengelolaan hotel</p>
        </div>
    </div>

    <script>
        // Print function
        function printReport() {
            window.print();
        }

        // Add smooth scrolling
        document.addEventListener('DOMContentLoaded', function() {
            // Smooth reveal animation for sections
            const sections = document.querySelectorAll('.section, .summary-box');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            sections.forEach(section => {
                section.style.opacity = '0';
                section.style.transform = 'translateY(20px)';
                section.style.transition = 'all 0.6s ease-out';
                observer.observe(section);
            });
        });
    </script>
</body>
</html>