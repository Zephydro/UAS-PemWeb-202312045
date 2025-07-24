<?php
session_start();
require_once '../config/koneksi.php';

// Redirect ke halaman yang sesuai berdasarkan role user
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/kamar.php");
    } else {
        header("Location: ../user/kamar.php");
    }
} else {
    // Jika belum login, redirect ke halaman kamar umum
    header("Location: ../kamar.php");
}
exit;
?>
