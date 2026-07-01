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

if (!function_exists('formatTanggal')) {
    function formatTanggal($tanggal)
    {
        return date('d-m-Y', strtotime($tanggal));
    }
}

if (!function_exists('formatTanggalIndonesia')) {
    function formatTanggalIndonesia($tanggal)
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ];
        $timestamp = strtotime($tanggal);

        return date('d', $timestamp) . ' ' . $bulan[(int) date('n', $timestamp)] . ' ' . date('Y', $timestamp);
    }
}
