<?php
session_start();
require_once '../config/koneksi.php';

// Redirect ke halaman yang sesuai berdasarkan role user
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    $query_string = $_SERVER['QUERY_STRING'] ? '?' . $_SERVER['QUERY_STRING'] : '';
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/booking_admin.php" . $query_string);
    } else {
        header("Location: ../user/booking.php" . $query_string);
    }
} else {
    // Jika belum login, redirect ke halaman login
    header("Location: login.php");
}
exit;
?>
