<?php
session_start();
require_once '../config/koneksi.php';

// Redirect ke halaman yang sesuai berdasarkan role user
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        // Admin bisa mengakses profil user atau dashboard
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../user/profil.php");
    }
} else {
    // Jika belum login, redirect ke halaman profil umum atau login
    if (file_exists('../profil.php')) {
        header("Location: ../profil.php");
    } else {
        header("Location: login.php");
    }
}
exit;
?>
