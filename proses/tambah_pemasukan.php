<?php
// Proses tambah data pemasukan baru ke tabel `incomes`.

require_once __DIR__ . '/../includes/auth_check.php';

$userId = (int) $_SESSION['user_id'];

$incomeDate  = trim($_POST['income_date'] ?? '');
$source      = trim($_POST['source'] ?? '');
$amount      = $_POST['amount'] ?? '';
$description = trim($_POST['description'] ?? '');

if ($incomeDate === '' || $source === '' || $amount === '' || (float) $amount <= 0) {
    $_SESSION['flash_error'] = 'Tanggal, sumber, dan jumlah wajib diisi dengan benar. Jumlah harus lebih dari 0.';
    header('Location: ' . BASE_URL . 'pages/pemasukan.php');
    exit;
}

$stmt = mysqli_prepare($conn, 'INSERT INTO incomes (user_id, source, amount, income_date, description) VALUES (?, ?, ?, ?, ?)');
mysqli_stmt_bind_param($stmt, 'isdss', $userId, $source, $amount, $incomeDate, $description);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['flash_success'] = 'Pemasukan berhasil ditambahkan.';
} else {
    $_SESSION['flash_error'] = 'Gagal memproses data.';
}
mysqli_stmt_close($stmt);

header('Location: ' . BASE_URL . 'pages/pemasukan.php');
exit;
