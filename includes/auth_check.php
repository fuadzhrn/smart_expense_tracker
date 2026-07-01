<?php
/**
 * Melindungi halaman agar hanya bisa diakses setelah login.
 * Include file ini di baris paling atas setiap halaman yang butuh login.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'auth/login.php');
    exit;
}
