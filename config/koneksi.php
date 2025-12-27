<?php
/**
 * ========================================
 * KONFIGURASI DATABASE
 * ========================================
 * File: config/koneksi.php
 * Fungsi: Koneksi ke database MySQL
 * Support: Local development & Railway production
 */

// Local development settings
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'inventaris_db';
$db_port = 3306;

// Koneksi ke database
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);

// Cek koneksi
if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

/**
 * Helper function untuk escape string
 */
function escape($string) {
    global $conn;
    return mysqli_real_escape_string($conn, trim($string));
}

/**
 * Helper function untuk alert Bootstrap
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