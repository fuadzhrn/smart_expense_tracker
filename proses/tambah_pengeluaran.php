<?php
// Proses tambah data pengeluaran baru ke tabel `expenses`.

require_once __DIR__ . '/../includes/auth_check.php';

$userId = (int) $_SESSION['user_id'];

$expenseDate = trim($_POST['expense_date'] ?? '');
$categoryId  = (int) ($_POST['category_id'] ?? 0);
$title       = trim($_POST['title'] ?? '');
$amount      = $_POST['amount'] ?? '';
$description = trim($_POST['description'] ?? '');

if ($expenseDate === '' || $categoryId <= 0 || $title === '' || $amount === '' || (float) $amount <= 0) {
    $_SESSION['flash_error'] = 'Tanggal, kategori, nama pengeluaran, dan jumlah wajib diisi dengan benar. Jumlah harus lebih dari 0.';
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

$stmt = mysqli_prepare($conn, 'INSERT INTO expenses (user_id, category_id, title, amount, expense_date, description) VALUES (?, ?, ?, ?, ?, ?)');
mysqli_stmt_bind_param($stmt, 'iisdss', $userId, $categoryId, $title, $amount, $expenseDate, $description);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['flash_success'] = 'Pengeluaran berhasil ditambahkan.';
} else {
    $_SESSION['flash_error'] = 'Gagal memproses data.';
}
mysqli_stmt_close($stmt);

header('Location: ' . BASE_URL . 'pages/pengeluaran.php');
exit;
