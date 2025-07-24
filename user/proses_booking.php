<?php
session_start();
require '../config/koneksi.php';
require '../config/helpers.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $room_id = (int)$_POST['room_id'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $no_hp = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $jumlah_tamu = (int)$_POST['jumlah_tamu'];
    $jumlah_malam = (int)$_POST['jumlah_malam'];
    $tanggal_checkin = mysqli_real_escape_string($conn, $_POST['tanggal_checkin']);
    $metode_pembayaran_id = (int)$_POST['metode_pembayaran_id'];
    $catatan = mysqli_real_escape_string($conn, $_POST['catatan']);
    $harga_per_malam = (int)$_POST['harga_per_malam'];
    $tipe_kamar = mysqli_real_escape_string($conn, $_POST['tipe_kamar']);
    
    // Hitung tanggal checkout
    $tanggal_checkout = date('Y-m-d', strtotime($tanggal_checkin . ' + ' . $jumlah_malam . ' days'));
    
    // Hitung total harga
    $total_harga = $harga_per_malam * $jumlah_malam;
    
    // Ambil informasi metode pembayaran
    $payment_query = "SELECT nama_metode FROM metode_pembayaran WHERE id = ? AND status = 'aktif'";
    $payment_stmt = $conn->prepare($payment_query);
    $payment_stmt->bind_param("i", $metode_pembayaran_id);
    $payment_stmt->execute();
    $payment_result = $payment_stmt->get_result();
    
    if ($payment_result->num_rows == 0) {
        logActivity($conn, $user_id, 'booking_failed', 'Gagal booking: Metode pembayaran tidak valid', $_SERVER['REMOTE_ADDR']);
        $_SESSION['error'] = "Metode pembayaran tidak valid.";
        header("Location: booking.php?room_id=" . $room_id);
        exit;
    }
    
    $payment_method = $payment_result->fetch_assoc();
    $metode_pembayaran = $payment_method['nama_metode'];
    
    try {
        // Mulai transaksi
        $conn->begin_transaction();
        
        // Insert booking
        $booking_query = "INSERT INTO booking (user_id, nama, no_hp, tipe_kamar, jumlah_tamu, jumlah_malam, catatan, harga_total, status) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'diajukan')";
        $booking_stmt = $conn->prepare($booking_query);
        $booking_stmt->bind_param("isssiiis", $user_id, $nama, $no_hp, $tipe_kamar, $jumlah_tamu, $jumlah_malam, $catatan, $total_harga);
        
        if (!$booking_stmt->execute()) {
            throw new Exception("Gagal menyimpan booking");
        }
        
        $booking_id = $conn->insert_id;
        
        // Insert pembayaran
        $payment_insert_query = "INSERT INTO pembayaran (booking_id, metode_pembayaran_id, jumlah, status) 
                                VALUES (?, ?, ?, 'belum_dibayar')";
        $payment_insert_stmt = $conn->prepare($payment_insert_query);
        $payment_insert_stmt->bind_param("iid", $booking_id, $metode_pembayaran_id, $total_harga);
        
        if (!$payment_insert_stmt->execute()) {
            throw new Exception("Gagal menyimpan data pembayaran");
        }
        
        // Commit transaksi
        $conn->commit();
        
        // Log aktivitas sukses
        logActivity($conn, $user_id, 'booking_success', "Berhasil booking kamar $tipe_kamar untuk $jumlah_malam malam", $_SERVER['REMOTE_ADDR']);
        
        // Kirim notifikasi ke user
        sendNotification($conn, $user_id, 'Booking Berhasil', "Booking kamar $tipe_kamar Anda telah berhasil. ID Booking: #$booking_id", 'success');
        
        // Kirim notifikasi ke admin
        $admin_query = "SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.nama_role = 'admin' LIMIT 1";
        $admin_result = mysqli_query($conn, $admin_query);
        if ($admin_result && $admin_row = mysqli_fetch_assoc($admin_result)) {
            sendNotification($conn, $admin_row['id'], 'Booking Baru', "Booking baru dari $nama untuk kamar $tipe_kamar. ID: #$booking_id", 'info');
        }
        
        // Redirect ke halaman transaksi
        header("Location: transaksi.php?booking_id=" . $booking_id);
        exit;
        
    } catch (Exception $e) {
        // Rollback transaksi jika ada error
        $conn->rollback();
        
        // Log aktivitas gagal
        logActivity($conn, $user_id, 'booking_failed', 'Gagal booking: ' . $e->getMessage(), $_SERVER['REMOTE_ADDR']);
        
        $_SESSION['error'] = "Terjadi kesalahan saat memproses booking. Silakan coba lagi.";
        header("Location: booking.php?room_id=" . $room_id);
        exit;
    }
} else {
    header("Location: kamar.php");
    exit;
}
?>
