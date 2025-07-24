<?php
session_start();
require_once '../config/koneksi.php';

// Redirect ke halaman yang sesuai berdasarkan role user
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/fasilitas.php");
    } else {
        header("Location: ../user/fasilitas.php");
    }
} else {
    // Jika belum login, redirect ke halaman fasilitas umum
    header("Location: ../fasilitas.php");
}
exit;
?>
