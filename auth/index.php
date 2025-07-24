<?php
session_start();
require_once '../config/koneksi.php';

// Router utama untuk folder auth
// Redirect ke halaman yang sesuai berdasarkan status login

if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    // User sudah login, redirect ke dashboard sesuai role
    if ($_SESSION['role'] === 'admin') {
        header("Location: ../admin/dashboard.php");
    } else {
        header("Location: ../user/dashboard.php");
    }
} else {
    // User belum login, redirect ke halaman utama atau login
    header("Location: ../index.php");
}
exit;
?>
