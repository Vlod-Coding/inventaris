<?php
/**
 * ========================================
 * KONEKSI DATABASE
 * ========================================
 * File: config/koneksi.php
 * Fungsi: Membuat koneksi ke database MySQL
 * Author: Sistem Inventaris
 * Date: 2025
 */

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'inventaris_db');

// Membuat koneksi menggunakan MySQLi
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi berhasil atau tidak
if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}

// Set charset UTF-8 untuk mendukung karakter Indonesia
mysqli_set_charset($conn, "utf8");

/**
 * Fungsi untuk mencegah SQL Injection
 * @param string $data - Data yang akan di-escape
 * @return string - Data yang sudah aman
 */
function escape($data) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($data));
}

/**
 * Fungsi untuk menampilkan alert Bootstrap
 * @param string $type - Tipe alert (success, danger, warning, info)
 * @param string $message - Pesan yang akan ditampilkan
 * @return string - HTML alert
 */
function alert($type, $message) {
    return '<div class="alert alert-'.$type.' alert-dismissible fade show" role="alert">
                '.$message.'
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
}

/**
 * Fungsi untuk format tanggal Indonesia
 * @param string $date - Tanggal format Y-m-d
 * @return string - Tanggal format Indonesia
 */
function tgl_indo($date) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    $pecah = explode('-', $date);
    return $pecah[2] . ' ' . $bulan[(int)$pecah[1]] . ' ' . $pecah[0];
}

/**
 * Fungsi untuk format rupiah
 * @param int $angka - Angka yang akan diformat
 * @return string - Format rupiah
 */
function rupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}
?>