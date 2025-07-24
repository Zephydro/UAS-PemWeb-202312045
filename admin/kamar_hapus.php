<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    // Ambil data kamar yang akan dihapus
    $query = "SELECT foto FROM rooms WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $room = mysqli_fetch_assoc($result);
    
    if ($room) {
        // Hapus foto jika ada
        if ($room['foto'] && file_exists("../uploads/" . $room['foto'])) {
            unlink("../uploads/" . $room['foto']);
        }
        
        // Hapus data kamar dari database
        $delete_query = "DELETE FROM rooms WHERE id = ?";
        $delete_stmt = mysqli_prepare($conn, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "i", $id);
        
        if (mysqli_stmt_execute($delete_stmt)) {
            $_SESSION['success'] = "Kamar berhasil dihapus!";
        } else {
            $_SESSION['error'] = "Gagal menghapus kamar!";
        }
    } else {
        $_SESSION['error'] = "Kamar tidak ditemukan!";
    }
} else {
    $_SESSION['error'] = "ID kamar tidak valid!";
}

header("Location: kamar.php");
exit;
?>
