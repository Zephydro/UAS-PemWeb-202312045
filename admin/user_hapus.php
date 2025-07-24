<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Cek apakah user yang akan dihapus bukan admin yang sedang login
    if ($id == $_SESSION['user_id']) {
        $_SESSION['error'] = "Anda tidak dapat menghapus akun Anda sendiri!";
        header("Location: users.php");
        exit;
    }
    
    // Hapus user menggunakan prepared statement
    $query = "DELETE FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['success'] = "User berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus user!";
    }
} else {
    $_SESSION['error'] = "ID user tidak valid!";
}

header("Location: users.php");
exit;
?>
