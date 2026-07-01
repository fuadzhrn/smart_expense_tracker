<?php
// Proses hapus kategori pada tabel `expense_categories` berdasarkan id.

require_once __DIR__ . '/../includes/auth_check.php';

$userId = (int) $_SESSION['user_id'];
$id     = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['flash_error'] = 'Gagal memproses data.';
    header('Location: ' . BASE_URL . 'pages/kategori.php');
    exit;
}

// Pastikan kategori masih ada dan milik user yang sedang login
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

// Cek apakah kategori masih dipakai di tabel expenses
$check = mysqli_prepare($conn, 'SELECT COUNT(*) AS total FROM expenses WHERE category_id = ? AND user_id = ?');
mysqli_stmt_bind_param($check, 'ii', $id, $userId);
mysqli_stmt_execute($check);
$masihDipakai = (int) mysqli_fetch_assoc(mysqli_stmt_get_result($check))['total'] > 0;
mysqli_stmt_close($check);

if ($masihDipakai) {
    $_SESSION['flash_error'] = 'Kategori tidak bisa dihapus karena masih digunakan pada data pengeluaran.';
    header('Location: ' . BASE_URL . 'pages/kategori.php');
    exit;
}

$stmt = mysqli_prepare($conn, 'DELETE FROM expense_categories WHERE id = ? AND user_id = ?');
mysqli_stmt_bind_param($stmt, 'ii', $id, $userId);

if (mysqli_stmt_execute($stmt)) {
    $_SESSION['flash_success'] = 'Kategori berhasil dihapus.';
} else {
    $_SESSION['flash_error'] = 'Gagal memproses data.';
}
mysqli_stmt_close($stmt);

header('Location: ' . BASE_URL . 'pages/kategori.php');
exit;
