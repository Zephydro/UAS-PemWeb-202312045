<?php
session_start();
require_once('../config/koneksi.php');

// Ambil input
$nama = mysqli_real_escape_string($conn, $_POST['nama']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

// Cek apakah username/email sudah digunakan
$cek = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' OR email = '$email'");
if (mysqli_num_rows($cek) > 0) {
    $_SESSION['error'] = "Username atau email sudah digunakan!";
    header("Location: register.php");
    exit;
}

// Role user = cari ID-nya
$roleQuery = mysqli_query($conn, "SELECT id FROM roles WHERE nama_role = 'user' LIMIT 1");
$roleData = mysqli_fetch_assoc($roleQuery);
$role_id = $roleData ? $roleData['id'] : 2; // default 2 kalau tidak ada

// Simpan ke database
$query = "INSERT INTO users (username, password, email, nama_lengkap, role_id)
          VALUES ('$username', '$password', '$email', '$nama', '$role_id')";

if (mysqli_query($conn, $query)) {
    $_SESSION['success'] = "Akun berhasil dibuat! Silakan login.";
    header("Location: register.php");
    exit;
} else {
    $_SESSION['error'] = "Gagal mendaftar, coba lagi.";
    header("Location: register.php");
    exit;
}
?>
