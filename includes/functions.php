<?php
/**
 * Kumpulan fungsi bantuan yang dipakai di banyak halaman.
 */

if (!function_exists('formatRupiah')) {
    function formatRupiah($angka)
    {
        return 'Rp' . number_format((float) $angka, 0, ',', '.');
    }
}

if (!function_exists('flash')) {
    function flash($key)
    {
        if (isset($_SESSION[$key])) {
            $value = $_SESSION[$key];
            unset($_SESSION[$key]);
            return $value;
        }
        return null;
    }
}
