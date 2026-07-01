<?php
// Proses edit data pemasukan pada tabel `incomes` berdasarkan id.

require_once __DIR__ . '/../includes/auth_check.php';

$userId = (int) $_SESSION['user_id'];

$id          = (int) ($_POST['id'] ?? 0);
$incomeDate  = trim($_POST['income_date'] ?? '');
$source      = trim($_POST['source'] ?? '');
$amount      = $_POST['amount'] ?? '';
$description = trim($_POST['description'] ?? '');

if ($id <= 0 || $incomeDate === '' || $source === '' || $amount === '' || (float) $amount <= 0) {
    $_SESSION['flash_error'] = 'Tanggal, sumber, dan jumlah wajib diisi dengan benar. Jumlah harus lebih dari 0.';
    header('Location: ' . BASE_URL . 'pages/pemasukan.php');
    exit;
}

// Pastikan data yang diedit benar-benar milik user yang sedang login
$check = mysqli_prepare($conn, 'SELECT id FROM incomes WHERE id = ? AND user_id = ?');
mysqli_stmt_bind_param($check, 'ii', $id, $userId);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);
$isOwner = mysqli_stmt_num_rows($check) > 0;
mysqli_stmt_close($check);

if (!$isOwner) {
    $_SESSION['flash_error'] = 'Data tidak ditemukan.';
    header('Location: ' . BASE_URL . 'pages/pemasukan.php');
    exit;
}

$stmt = mysqli_prepare($conn, 'UPDATE incomes SET source = ?, amount = ?, income_date = ?, description = ? WHERE id = ? AND user_id = ?');
mysqli_stmt_bind_param($stmt, 'sdssii', $source, $amount, $incomeDate, $description, $id, $userId);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['flash_success'] = 'Pemasukan berhasil diperbarui.';
} else {
    $_SESSION['flash_error'] = 'Gagal memproses data.';
}
mysqli_stmt_close($stmt);

header('Location: ' . BASE_URL . 'pages/pemasukan.php');
exit;
