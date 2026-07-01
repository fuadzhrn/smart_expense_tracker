<?php
// Proses edit kategori pada tabel `expense_categories` berdasarkan id.

require_once __DIR__ . '/../includes/auth_check.php';

$userId      = (int) $_SESSION['user_id'];
$id          = (int) ($_POST['id'] ?? 0);
$name        = trim($_POST['name'] ?? '');
$description = trim($_POST['description'] ?? '');

if ($id <= 0 || $name === '') {
    $_SESSION['flash_error'] = 'Nama kategori wajib diisi.';
    header('Location: ' . BASE_URL . 'pages/kategori.php');
    exit;
}

// Pastikan kategori benar-benar milik user yang sedang login
$check = mysqli_prepare($conn, 'SELECT id FROM expense_categories WHERE id = ? AND user_id = ?');
mysqli_stmt_bind_param($check, 'ii', $id, $userId);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);
$isOwner = mysqli_stmt_num_rows($check) > 0;
mysqli_stmt_close($check);

if (!$isOwner) {
    $_SESSION['flash_error'] = 'Data tidak ditemukan.';
    header('Location: ' . BASE_URL . 'pages/kategori.php');
    exit;
}

// Nama kategori tidak boleh sama dengan kategori lain milik user yang sama
$check = mysqli_prepare($conn, 'SELECT id FROM expense_categories WHERE user_id = ? AND LOWER(name) = LOWER(?) AND id != ? LIMIT 1');
mysqli_stmt_bind_param($check, 'isi', $userId, $name, $id);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);
$isDuplicate = mysqli_stmt_num_rows($check) > 0;
mysqli_stmt_close($check);

if ($isDuplicate) {
    $_SESSION['flash_error'] = 'Nama kategori sudah digunakan.';
    header('Location: ' . BASE_URL . 'pages/kategori.php');
    exit;
}

$stmt = mysqli_prepare($conn, 'UPDATE expense_categories SET name = ?, description = ? WHERE id = ? AND user_id = ?');
mysqli_stmt_bind_param($stmt, 'ssii', $name, $description, $id, $userId);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['flash_success'] = 'Kategori berhasil diperbarui.';
} else {
    $_SESSION['flash_error'] = 'Gagal memproses data.';
}
mysqli_stmt_close($stmt);

header('Location: ' . BASE_URL . 'pages/kategori.php');
exit;
