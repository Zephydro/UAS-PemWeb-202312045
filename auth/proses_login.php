<?php
session_start();
require '../config/koneksi.php';
require '../config/helpers.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Gunakan username ATAU email
$query = "SELECT u.*, r.nama_role FROM users u
          JOIN roles r ON u.role_id = r.id
          WHERE u.username = ? OR u.email = ? LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if ($password === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = strtolower(trim($user['nama_role']));

        // Log aktivitas login
        logActivity($conn, $user['id'], 'Login', 'User berhasil login ke sistem', $_SERVER['REMOTE_ADDR']);
        
        // Kirim notifikasi selamat datang untuk user
        if ($_SESSION['role'] == 'user') {
            sendNotification($conn, $user['id'], 'Selamat Datang!', 'Selamat datang di HotelEase. Terima kasih telah bergabung dengan kami.', 'info');
        }

        // Redirect sesuai role (hanya admin dan user)
        switch ($_SESSION['role']) {
            case 'admin':
                header("Location: ../admin/dashboard.php");
                break;
            case 'user':
                header("Location: ../user/dashboard.php");
                break;
            default:
                // Debug: tampilkan role yang ditemukan
                $_SESSION['error'] = "Role tidak valid: '" . $user['nama_role'] . "' (processed: '" . $_SESSION['role'] . "'). Hanya role 'admin' dan 'user' yang diizinkan.";
                header("Location: login.php");
        }
        exit;
    } else {
        // Log percobaan login gagal
        if (isset($user['id'])) {
            logActivity($conn, $user['id'], 'Login Gagal', 'Percobaan login dengan password salah', $_SERVER['REMOTE_ADDR']);
        }
        $_SESSION['error'] = "Password salah.";
        header("Location: login.php");
        exit;
    }
} else {
    $_SESSION['error'] = "User tidak ditemukan.";
    header("Location: login.php");
    exit;
    
}
