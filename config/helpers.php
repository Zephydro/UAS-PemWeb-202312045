<?php
// Helper function untuk mencatat log aktivitas
function logActivity($conn, $user_id, $activity, $description = '', $ip_address = null) {
    if ($ip_address === null) {
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    }
    
    $user_id = (int)$user_id;
    $activity = mysqli_real_escape_string($conn, $activity);
    $description = mysqli_real_escape_string($conn, $description);
    $ip_address = mysqli_real_escape_string($conn, $ip_address);
    
    $query = "INSERT INTO log_aktivitas (user_id, aktivitas, deskripsi, ip_address, tanggal) 
              VALUES ($user_id, '$activity', '$description', '$ip_address', NOW())";
    
    mysqli_query($conn, $query);
}

// Helper function untuk mengirim notifikasi
function sendNotification($conn, $user_id, $title, $message, $type = 'info') {
    $user_id = (int)$user_id;
    $title = mysqli_real_escape_string($conn, $title);
    $message = mysqli_real_escape_string($conn, $message);
    $type = mysqli_real_escape_string($conn, $type);
    
    $query = "INSERT INTO notifikasi (user_id, judul, pesan, tipe, status, tanggal) 
              VALUES ($user_id, '$title', '$message', '$type', 'belum_dibaca', NOW())";
    
    mysqli_query($conn, $query);
}

// Helper function untuk mendapatkan nama user
function getUserName($conn, $user_id) {
    $user_id = (int)$user_id;
    $query = "SELECT nama FROM users WHERE id = $user_id";
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['nama'];
    }
    
    return 'Unknown User';
}

// Helper function untuk format currency
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

// Helper function untuk format date
function formatDate($date, $format = 'd/m/Y H:i') {
    return date($format, strtotime($date));
}

// Helper function untuk mendapatkan status badge
function getStatusBadge($status) {
    $badges = [
        'pending' => 'bg-warning',
        'diterima' => 'bg-success',
        'ditolak' => 'bg-danger',
        'dibayar' => 'bg-success',
        'belum_dibayar' => 'bg-warning',
        'aktif' => 'bg-success',
        'nonaktif' => 'bg-secondary',
        'read' => 'bg-info',
        'unread' => 'bg-warning'
    ];
    
    $class = $badges[$status] ?? 'bg-secondary';
    return "<span class='badge $class'>" . ucfirst(str_replace('_', ' ', $status)) . "</span>";
}

// Helper function untuk validasi input
function validateInput($data, $rules) {
    $errors = [];
    
    foreach ($rules as $field => $rule) {
        $value = $data[$field] ?? '';
        
        if (isset($rule['required']) && $rule['required'] && empty($value)) {
            $errors[$field] = $rule['label'] . ' wajib diisi';
            continue;
        }
        
        if (!empty($value)) {
            if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
                $errors[$field] = $rule['label'] . ' minimal ' . $rule['min_length'] . ' karakter';
            }
            
            if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
                $errors[$field] = $rule['label'] . ' maksimal ' . $rule['max_length'] . ' karakter';
            }
            
            if (isset($rule['email']) && $rule['email'] && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = $rule['label'] . ' harus berupa email yang valid';
            }
            
            if (isset($rule['numeric']) && $rule['numeric'] && !is_numeric($value)) {
                $errors[$field] = $rule['label'] . ' harus berupa angka';
            }
            
            if (isset($rule['min_value']) && is_numeric($value) && $value < $rule['min_value']) {
                $errors[$field] = $rule['label'] . ' minimal ' . $rule['min_value'];
            }
        }
    }
    
    return $errors;
}

// Helper function untuk generate random string
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    
    return $randomString;
}

// Helper function untuk upload file
function uploadFile($file, $uploadDir, $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']) {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return ['success' => false, 'message' => 'Tidak ada file yang dipilih'];
    }
    
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    
    if ($fileError !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Error saat upload file'];
    }
    
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    if (!in_array($fileExt, $allowedTypes)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan'];
    }
    
    if ($fileSize > 5000000) { // 5MB
        return ['success' => false, 'message' => 'Ukuran file terlalu besar (maksimal 5MB)'];
    }
    
    $newFileName = generateRandomString(20) . '.' . $fileExt;
    $uploadPath = $uploadDir . '/' . $newFileName;
    
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    if (move_uploaded_file($fileTmpName, $uploadPath)) {
        return ['success' => true, 'filename' => $newFileName, 'path' => $uploadPath];
    } else {
        return ['success' => false, 'message' => 'Gagal menyimpan file'];
    }
}

// Helper function untuk pagination
function paginate($conn, $query, $page = 1, $perPage = 10) {
    $page = max(1, (int)$page);
    $offset = ($page - 1) * $perPage;
    
    // Count total records
    $countQuery = preg_replace('/SELECT .* FROM/i', 'SELECT COUNT(*) as total FROM', $query);
    $countResult = mysqli_query($conn, $countQuery);
    $totalRecords = mysqli_fetch_assoc($countResult)['total'];
    
    // Get paginated data
    $paginatedQuery = $query . " LIMIT $perPage OFFSET $offset";
    $result = mysqli_query($conn, $paginatedQuery);
    
    $data = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $data[] = $row;
    }
    
    $totalPages = ceil($totalRecords / $perPage);
    
    return [
        'data' => $data,
        'current_page' => $page,
        'total_pages' => $totalPages,
        'total_records' => $totalRecords,
        'per_page' => $perPage
    ];
}

// Helper function untuk render pagination
function renderPagination($currentPage, $totalPages, $baseUrl) {
    if ($totalPages <= 1) return '';
    
    $html = '<nav><ul class="pagination justify-content-center">';
    
    // Previous button
    if ($currentPage > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&page=' . ($currentPage - 1) . '">Previous</a></li>';
    }
    
    // Page numbers
    $start = max(1, $currentPage - 2);
    $end = min($totalPages, $currentPage + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        $active = $i == $currentPage ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $baseUrl . '&page=' . $i . '">' . $i . '</a></li>';
    }
    
    // Next button
    if ($currentPage < $totalPages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '&page=' . ($currentPage + 1) . '">Next</a></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}
?>