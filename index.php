<?php
/**
 * Halaman awal: arahkan ke dashboard jika sudah login,
 * atau ke halaman login jika belum.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/koneksi.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'pages/dashboard.php');
} else {
    header('Location: ' . BASE_URL . 'auth/login.php');
}
exit;
