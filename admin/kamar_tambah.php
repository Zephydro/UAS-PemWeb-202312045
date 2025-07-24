<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomor_kamar = $_POST['nomor_kamar'];
    $tipe_kamar = $_POST['tipe_kamar'];
    $harga_per_malam = $_POST['harga_per_malam'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status'];

    // Upload foto
    $foto_name = $_FILES['foto']['name'];
    $foto_tmp = $_FILES['foto']['tmp_name'];
    $folder = "../uploads/" . $foto_name;

    if (move_uploaded_file($foto_tmp, $folder)) {
        $query = "INSERT INTO rooms (nomor_kamar, tipe_kamar, harga_per_malam, deskripsi, foto, status)
                  VALUES ('$nomor_kamar', '$tipe_kamar', '$harga_per_malam', '$deskripsi', '$foto_name', '$status')";
        mysqli_query($conn, $query);
        header("Location: kamar.php");
        exit;
    } else {
        $error = "Upload foto gagal!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Kamar - HotelEase</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="admin-body">
<div class="d-flex">
    <?php include 'sidebar.php'; ?>
    <main class="flex-grow-1 p-4">
        <h2 class="fw-bold text-green mb-4">Tambah Kamar</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Nomor Kamar</label>
                <input type="text" name="nomor_kamar" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tipe Kamar</label>
                <input type="text" name="tipe_kamar" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Harga per Malam</label>
                <input type="number" name="harga_per_malam" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="4"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Foto Kamar</label>
                <input type="file" name="foto" class="form-control" accept="image/*" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required>
                    <option value="tersedia">Tersedia</option>
                    <option value="terisi">Terisi</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Simpan</button>
            <a href="kamar.php" class="btn btn-secondary">Batal</a>
        </form>
    </main>
</div>
</body>
</html>
