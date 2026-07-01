<?php
// Proses hapus data pengeluaran pada tabel `expenses` berdasarkan id.

require_once __DIR__ . '/../includes/auth_check.php';

$userId = (int) $_SESSION['user_id'];
$id     = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    $stmt = mysqli_prepare($conn, 'DELETE FROM expenses WHERE id = ? AND user_id = ?');
    mysqli_stmt_bind_param($stmt, 'ii', $id, $userId);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        $_SESSION['flash_success'] = 'Pengeluaran berhasil dihapus.';
    } else {
        $_SESSION['flash_error'] = 'Data tidak ditemukan.';
    }
    mysqli_stmt_close($stmt);
} else {
    $_SESSION['flash_error'] = 'Gagal memproses data.';
}

header('Location: ' . BASE_URL . 'pages/pengeluaran.php');
exit;
