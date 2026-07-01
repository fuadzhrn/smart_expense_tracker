<?php
/**
 * Koneksi ke database MySQL: smart_expense_tracker
 */

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'smart_expense_tracker';

$koneksi = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

mysqli_set_charset($koneksi, 'utf8mb4');

// BASE_URL dihitung otomatis agar semua link/asset tetap benar
// baik project diakses lewat http://localhost/Smart_Expense_Tracker/
// maupun lewat virtual host (mis. http://smart-expense-tracker.test/).
if (!defined('BASE_URL')) {
    $docRoot     = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']) : '';
    $projectRoot = str_replace('\\', '/', dirname(__DIR__));
    $basePath    = '';

    if ($docRoot !== '' && strpos($projectRoot, $docRoot) === 0) {
        $basePath = substr($projectRoot, strlen($docRoot));
    }

    define('BASE_URL', rtrim($basePath, '/') . '/');
}
