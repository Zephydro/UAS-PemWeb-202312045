<?php
session_start();
require '../config/koneksi.php';
require '../config/helpers.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    echo json_encode(['success' => false, 'message' => 'Akses tidak diizinkan']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['success' => false, 'message' => 'Metode tidak diizinkan']);
    exit;
}

$booking_id = isset($_POST['booking_id']) ? (int)$_POST['booking_id'] : 0;
$keterangan = isset($_POST['keterangan']) ? trim($_POST['keterangan']) : '';

// Validasi booking_id dan pastikan milik user yang login
$check_query = "SELECT id, status FROM booking WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($check_query);
$stmt->bind_param("ii", $booking_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Booking tidak ditemukan']);
    exit;
}

$booking = $result->fetch_assoc();

// Cek apakah ada file yang diupload
if (!isset($_FILES['bukti_pembayaran']) || $_FILES['bukti_pembayaran']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'File bukti pembayaran harus diupload']);
    exit;
}

$file = $_FILES['bukti_pembayaran'];

// Validasi tipe file (untuk pembelajaran, terima semua gambar)
$allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'application/pdf'];
if (!in_array($file['type'], $allowed_types)) {
    echo json_encode(['success' => false, 'message' => 'Tipe file tidak didukung. Gunakan JPG, PNG, GIF, WEBP, atau PDF']);
    exit;
}

// Validasi ukuran file (maksimal 10MB untuk pembelajaran)
$max_size = 10 * 1024 * 1024; // 10MB
if ($file['size'] > $max_size) {
    echo json_encode(['success' => false, 'message' => 'Ukuran file terlalu besar. Maksimal 10MB']);
    exit;
}

// Buat nama file yang unik
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'bukti_' . $booking_id . '_' . time() . '_' . uniqid() . '.' . $extension;

// Pastikan folder uploads ada
$upload_dir = '../uploads/bukti_pembayaran/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$upload_path = $upload_dir . $filename;

// Upload file
if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
    echo json_encode(['success' => false, 'message' => 'Gagal mengupload file']);
    exit;
}

try {
    // Mulai transaksi
    $conn->begin_transaction();
    
    // Update status pembayaran
    $update_payment = "UPDATE pembayaran SET status = 'menunggu_verifikasi', 
                       bukti_pembayaran = ?, keterangan_pembayaran = ?, 
                       tanggal_upload = NOW() 
                       WHERE booking_id = ?";
    $stmt = $conn->prepare($update_payment);
    $stmt->bind_param("ssi", $filename, $keterangan, $booking_id);
    
    if (!$stmt->execute()) {
        throw new Exception("Gagal mengupdate data pembayaran");
    }
    
    // Update status booking jika diperlukan
    if ($booking['status'] == 'diajukan') {
        $update_booking = "UPDATE booking SET status = 'menunggu_konfirmasi' WHERE id = ?";
        $stmt2 = $conn->prepare($update_booking);
        $stmt2->bind_param("i", $booking_id);
        $stmt2->execute();
    }
    
    // Commit transaksi
    $conn->commit();
    
    // Log aktivitas
    logActivity($conn, $_SESSION['user_id'], 'upload_bukti', "Upload bukti pembayaran untuk booking #$booking_id", $_SERVER['REMOTE_ADDR']);
    
    // Kirim notifikasi ke admin
    $admin_query = "SELECT u.id FROM users u JOIN roles r ON u.role_id = r.id WHERE r.nama_role = 'admin' LIMIT 1";
    $admin_result = mysqli_query($conn, $admin_query);
    if ($admin_result && $admin_row = mysqli_fetch_assoc($admin_result)) {
        sendNotification($conn, $admin_row['id'], 'Bukti Pembayaran Baru', "Bukti pembayaran untuk booking #$booking_id telah diupload", 'info');
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Bukti pembayaran berhasil diupload. Menunggu verifikasi admin.',
        'filename' => $filename
    ]);
    
} catch (Exception $e) {
    // Rollback dan hapus file jika ada error
    $conn->rollback();
    if (file_exists($upload_path)) {
        unlink($upload_path);
    }
    
    echo json_encode(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
}
?>
