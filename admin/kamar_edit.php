<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'];
$query = mysqli_query($conn, "SELECT * FROM rooms WHERE id = $id");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "Data tidak ditemukan!";
    exit;
}

// Saat form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nomor_kamar = $_POST['nomor_kamar'];
    $tipe_kamar = $_POST['tipe_kamar'];
    $harga_per_malam = $_POST['harga_per_malam'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status'];

    // Cek apakah ada file foto baru diupload
    if (!empty($_FILES['foto']['name'])) {
        $foto_name = $_FILES['foto']['name'];
        $foto_tmp = $_FILES['foto']['tmp_name'];
        $folder = "../uploads/" . $foto_name;

        if (move_uploaded_file($foto_tmp, $folder)) {
            $query = "UPDATE rooms SET 
                        nomor_kamar='$nomor_kamar', 
                        tipe_kamar='$tipe_kamar', 
                        harga_per_malam='$harga_per_malam', 
                        deskripsi='$deskripsi',
                        foto='$foto_name',
                        status='$status'
                      WHERE id = $id";
        } else {
            $error = "Upload foto gagal!";
        }
    } else {
        // Tidak ganti foto
        $query = "UPDATE rooms SET 
                    nomor_kamar='$nomor_kamar', 
                    tipe_kamar='$tipe_kamar', 
                    harga_per_malam='$harga_per_malam', 
                    deskripsi='$deskripsi',
                    status='$status'
                  WHERE id = $id";
    }

    if (mysqli_query($conn, $query)) {
        header("Location: kamar.php");
        exit;
    } else {
        $error = "Gagal mengupdate data!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Kamar - HotelEase</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="admin-body">
<div class="d-flex">
    <?php include 'sidebar.php'; ?>
    <main class="flex-grow-1 p-4">
        <h2 class="fw-bold text-green mb-4">Edit Kamar</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Nomor Kamar</label>
                <input type="text" name="nomor_kamar" class="form-control" value="<?= $data['nomor_kamar'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Tipe Kamar</label>
                <input type="text" name="tipe_kamar" class="form-control" value="<?= $data['tipe_kamar'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Harga per Malam</label>
                <input type="number" name="harga_per_malam" class="form-control" value="<?= $data['harga_per_malam'] ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="4"><?= $data['deskripsi'] ?></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Foto Saat Ini</label><br>
                <img src="../uploads/<?= $data['foto'] ?>" width="150" alt="Foto Kamar">
            </div>
            <div class="mb-3">
                <label class="form-label">Ganti Foto (Opsional)</label>
                <input type="file" name="foto" class="form-control" accept="image/*">
            </div>
            <div class="mb-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="tersedia" <?= $data['status'] == 'tersedia' ? 'selected' : '' ?>>Tersedia</option>
                    <option value="terisi" <?= $data['status'] == 'terisi' ? 'selected' : '' ?>>Terisi</option>
                </select>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
            <a href="kamar.php" class="btn btn-secondary">Batal</a>
        </form>
    </main>
</div>

</body>
</html>
