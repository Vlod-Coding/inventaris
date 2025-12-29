<?php
/**
 * ========================================
 * KONFIGURASI DATABASE
 * ========================================
 * File: config/koneksi.php
 * Fungsi: Koneksi ke database MySQL
 * Support: Local development & Production (InfinityFree/Railway)
 */

// Set timezone ke Indonesia (WIB)
date_default_timezone_set('Asia/Jakarta');

// Deteksi environment
$is_production = (
    isset($_SERVER['HTTP_HOST']) && 
    (strpos($_SERVER['HTTP_HOST'], 'infinityfreeapp.com') !== false || 
     strpos($_SERVER['HTTP_HOST'], 'free.nf') !== false ||
     strpos($_SERVER['HTTP_HOST'], 'railway.app') !== false ||
     strpos($_SERVER['HTTP_HOST'], 'up.railway.app') !== false)
);

if ($is_production) {
    // ===== PRODUCTION SETTINGS =====
    // Ganti dengan informasi database dari hosting Anda
    $db_host = 'sql100.infinityfree.com'; // MySQL hostname
    $db_user = 'if0_40771316'; // MySQL username
    $db_pass = 'Bajudit0k0'; // MySQL password
    $db_name = 'if0_40771316_inventaris'; // Database name
    
    // Disable error display di production
    error_reporting(0);
    ini_set('display_errors', 0);
    
    // Connect without port for InfinityFree
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
} else {
    // ===== LOCAL DEVELOPMENT SETTINGS =====
    $db_host = 'localhost';
    $db_user = 'root';
    $db_pass = '';
    $db_name = 'inventaris_db';
    $db_port = 3306;
    
    // Enable error display di development
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    // Connect with port for localhost
    $conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name, $db_port);
}

// Cek koneksi
if (!$conn) {
    if ($is_production) {
        // Log error untuk debugging
        error_log("Database connection failed: " . mysqli_connect_error());
        die("Database connection failed. Please try again later.");
    } else {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");

// Set MySQL timezone ke WIB (UTC+7)
mysqli_query($conn, "SET time_zone = '+07:00'");

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