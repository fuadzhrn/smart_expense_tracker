<?php
// Proses tambah kategori baru ke tabel `expense_categories`.

require_once __DIR__ . '/../includes/auth_check.php';

$userId      = (int) $_SESSION['user_id'];
$name        = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($name === '') {
    $_SESSION['flash_error'] = 'Nama kategori wajib diisi.';
    header('Location: ' . BASE_URL . 'pages/kategori.php');
    exit;
}

$check = mysqli_prepare($conn, 'SELECT id FROM expense_categories WHERE user_id = ? AND LOWER(name) = LOWER(?) LIMIT 1');
mysqli_stmt_bind_param($check, 'is', $userId, $name);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);
$isDuplicate = mysqli_stmt_num_rows($check) > 0;
mysqli_stmt_close($check);

if ($isDuplicate) {
    $_SESSION['flash_error'] = 'Nama kategori sudah digunakan.';
    header('Location: ' . BASE_URL . 'pages/kategori.php');
    exit;
}

$stmt = mysqli_prepare($conn, 'INSERT INTO expense_categories (user_id, name, description) VALUES (?, ?, ?)');
mysqli_stmt_bind_param($stmt, 'iss', $userId, $name, $description);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['flash_success'] = 'Kategori berhasil ditambahkan.';
} else {
    $_SESSION['flash_error'] = 'Gagal memproses data.';
}
mysqli_stmt_close($stmt);

header('Location: ' . BASE_URL . 'pages/kategori.php');
exit;
