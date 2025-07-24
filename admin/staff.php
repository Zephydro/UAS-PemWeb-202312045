<?php
session_start();
require '../config/koneksi.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM staff WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Staff berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Gagal menghapus staff!";
    }
    header("Location: staff.php");
    exit;
}

// Handle Add
if (isset($_POST['add'])) {
    $name = $_POST['name'];
    $work = $_POST['work'];
    $stmt = $conn->prepare("INSERT INTO staff (name, work) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $work);
    if ($stmt->execute()) {
        $_SESSION['success'] = "Staff berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambahkan staff!";
    }
    header("Location: staff.php");
    exit;
}

// Handle Update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = trim($_POST['name']);
    $work = trim($_POST['work']);
    
    if (!empty($name) && !empty($work)) {
        $stmt = $conn->prepare("UPDATE staff SET name = ?, work = ? WHERE id = ?");
        $stmt->bind_param("ssi", $name, $work, $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Staff berhasil diupdate!";
        } else {
            $_SESSION['error'] = "Gagal mengupdate staff!";
        }
    } else {
        $_SESSION['error'] = "Nama dan pekerjaan tidak boleh kosong!";
    }
    header("Location: staff.php");
    exit;
}

// Get staff list
$result = $conn->query("SELECT * FROM staff ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Staff - HotelEase</title>
    <link rel="stylesheet" href="../assets/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .avatar-circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
        }
        
        .modal-backdrop {
            backdrop-filter: blur(3px);
        }
        
        .form-control.is-invalid {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }
        
        .table tbody tr:hover {
            background-color: rgba(0, 123, 255, 0.1);
            transition: background-color 0.2s;
        }
        
        .btn-group .btn {
            transition: all 0.3s ease;
        }
        
        .btn-group .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        /* Fix modal z-index issues */
        .modal {
            z-index: 1055;
        }
        
        .modal-backdrop {
            z-index: 1050;
        }
    </style>
</head>
<body class="admin-body">
    <!-- Hamburger panel will be injected by hamburger-universal.js -->
    <?php require('sidebar.php'); ?>

<div class="sidebar-overlay"></div>
<button class="hamburger-btn">
    <div class="hamburger-icon">
        <span></span>
        <span></span>
        <span></span>
    </div>
</button>
    
    <div class="main-content">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
                <h2 class="fw-bold text-gold">
                    <i class="bi bi-people me-2"></i>Manajemen Staff
                </h2>
            </div>

            <!-- Alert Messages -->
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show shadow-sm fade-in-up" role="alert">
                    <i class="bi bi-check-circle me-2"></i><?= $_SESSION['success'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show shadow-sm fade-in-up" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= $_SESSION['error'] ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Statistics Card -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card stats-card bg-gradient-primary text-black shadow-lg fade-in-up">
                        <div class="card-body text-center">
                            <i class="bi bi-people fs-1 mb-2"></i>
                            <h5>Total Staff</h5>
                            <h3 class="fw-bold"><?= mysqli_num_rows($result) ?></h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Tambah Staff -->
            <div class="card shadow-lg mb-4 fade-in-up">
                <div class="card-header bg-gradient-success text-black">
                    <h5 class="mb-0">
                        <i class="bi bi-person-plus me-2"></i>Tambah Staff Baru
                    </h5>
                </div>
                <div class="card-body">
                    <form action="" method="POST" class="row g-3">
                        <div class="col-md-5">
                            <label for="name" class="form-label fw-bold">
                                <i class="bi bi-person me-1"></i>Nama Staff
                            </label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Masukkan nama staff" required>
                        </div>
                        <div class="col-md-5">
                            <label for="work" class="form-label fw-bold">
                                <i class="bi bi-briefcase me-1"></i>Pekerjaan
                            </label>
                            <input type="text" name="work" id="work" class="form-control" placeholder="Masukkan pekerjaan" required>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" name="add" class="btn btn-success w-100 shadow-sm">
                                <i class="bi bi-plus-circle me-1"></i>Tambah
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Staff -->
            <div class="card shadow-lg fade-in-up">
                <div class="card-header bg-gradient-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-list-ul me-2"></i>Daftar Staff
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th><i class="bi bi-hash me-1"></i>ID</th>
                                    <th><i class="bi bi-person me-1"></i>Nama</th>
                                    <th><i class="bi bi-briefcase me-1"></i>Pekerjaan</th>
                                    <th><i class="bi bi-gear me-1"></i>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                // Reset result pointer
                                mysqli_data_seek($result, 0);
                                while ($row = $result->fetch_assoc()): 
                                ?>
                                <tr>
                                    <td><span class="badge bg-secondary"><?= $row['id']; ?></span></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary text-white me-2">
                                                <?= strtoupper(substr($row['name'], 0, 1)) ?>
                                            </div>
                                            <strong><?= htmlspecialchars($row['name']); ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info"><?= htmlspecialchars($row['work']); ?></span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id']; ?>" title="Edit Staff">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <a href="?delete=<?= $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus staff ini?')" class="btn btn-sm btn-outline-danger" title="Hapus Staff">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal Edit -->
                                <div class="modal fade" id="editModal<?= $row['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-gradient-primary text-white">
                                                <h5 class="modal-title">
                                                    <i class="bi bi-pencil me-2"></i>Edit Staff
                                                </h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="" method="POST">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?= $row['id']; ?>">
                                                    <div class="mb-3">
                                                        <label for="edit_name<?= $row['id']; ?>" class="form-label fw-bold">
                                                            <i class="bi bi-person me-1"></i>Nama Staff
                                                        </label>
                                                        <input type="text" name="name" id="edit_name<?= $row['id']; ?>" class="form-control" value="<?= htmlspecialchars($row['name']); ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="edit_work<?= $row['id']; ?>" class="form-label fw-bold">
                                                            <i class="bi bi-briefcase me-1"></i>Pekerjaan
                                                        </label>
                                                        <input type="text" name="work" id="edit_work<?= $row['id']; ?>" class="form-control" value="<?= htmlspecialchars($row['work']); ?>" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                        <i class="bi bi-x-lg me-1"></i>Batal
                                                    </button>
                                                    <button type="submit" name="update" class="btn btn-primary">
                                                        <i class="bi bi-check-lg me-1"></i>Update
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/admin.js"></script>

<script>
// Handle form submissions
document.addEventListener('DOMContentLoaded', function() {
    // Close all modals when page loads (in case of stuck modals)
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        const modalInstance = bootstrap.Modal.getInstance(modal);
        if (modalInstance) {
            modalInstance.hide();
        }
    });

    // Handle form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const inputs = form.querySelectorAll('input[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (input.value.trim() === '') {
                    isValid = false;
                    input.classList.add('is-invalid');
                } else {
                    input.classList.remove('is-invalid');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang diperlukan!');
            }
        });
    });

    // Close modal after successful form submission
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('success') || urlParams.has('error')) {
        // Close any open modals
        modals.forEach(modal => {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        });
    }
});

// Function to reset form when modal is closed
function resetModalForm(modalId) {
    const modal = document.getElementById(modalId);
    const form = modal.querySelector('form');
    if (form) {
        form.reset();
        // Remove validation classes
        const inputs = form.querySelectorAll('.is-invalid');
        inputs.forEach(input => input.classList.remove('is-invalid'));
    }
}

// Add event listeners to modals
document.addEventListener('DOMContentLoaded', function() {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function() {
            resetModalForm(this.id);
        });
    });
});
</script>

</body>
</html>
