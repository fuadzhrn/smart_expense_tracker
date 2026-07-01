<?php
// Proses edit data pengeluaran pada tabel `expenses` berdasarkan id.

require_once __DIR__ . '/../includes/auth_check.php';

$userId = (int) $_SESSION['user_id'];

$id          = (int) ($_POST['id'] ?? 0);
$expenseDate = trim($_POST['expense_date'] ?? '');
$categoryId  = (int) ($_POST['category_id'] ?? 0);
$title       = trim($_POST['title'] ?? '');
$amount      = $_POST['amount'] ?? '';
$description = trim($_POST['description'] ?? '');

if ($id <= 0 || $expenseDate === '' || $categoryId <= 0 || $title === '' || $amount === '' || (float) $amount <= 0) {
    $_SESSION['flash_error'] = 'Tanggal, kategori, nama pengeluaran, dan jumlah wajib diisi dengan benar. Jumlah harus lebih dari 0.';
    header('Location: ' . BASE_URL . 'pages/pengeluaran.php');
    exit;
}

// Pastikan data pengeluaran ini milik user yang sedang login
$check = mysqli_prepare($conn, 'SELECT id FROM expenses WHERE id = ? AND user_id = ?');
mysqli_stmt_bind_param($check, 'ii', $id, $userId);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);
$isOwner = mysqli_stmt_num_rows($check) > 0;
mysqli_stmt_close($check);

if (!$isOwner) {
    $_SESSION['flash_error'] = 'Data tidak ditemukan.';
    header('Location: ' . BASE_URL . 'pages/pengeluaran.php');
    exit;
}

// Pastikan kategori benar-benar milik user yang sedang login
$check = mysqli_prepare($conn, 'SELECT id FROM expense_categories WHERE id = ? AND user_id = ?');
mysqli_stmt_bind_param($check, 'ii', $categoryId, $userId);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);
$categoryValid = mysqli_stmt_num_rows($check) > 0;
mysqli_stmt_close($check);

if (!$categoryValid) {
    $_SESSION['flash_error'] = 'Kategori pengeluaran belum tersedia. Silakan tambahkan kategori terlebih dahulu.';
    header('Location: ' . BASE_URL . 'pages/pengeluaran.php');
    exit;
}

$stmt = mysqli_prepare($conn, 'UPDATE expenses SET category_id = ?, title = ?, amount = ?, expense_date = ?, description = ? WHERE id = ? AND user_id = ?');
mysqli_stmt_bind_param($stmt, 'isdssii', $categoryId, $title, $amount, $expenseDate, $description, $id, $userId);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['flash_success'] = 'Pengeluaran berhasil diperbarui.';
} else {
    $_SESSION['flash_error'] = 'Gagal memproses data.';
}
mysqli_stmt_close($stmt);

header('Location: ' . BASE_URL . 'pages/pengeluaran.php');
exit;
