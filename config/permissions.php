<?php
/**
 * ========================================
 * PERMISSION HELPER FUNCTIONS
 * ========================================
 * File: config/permissions.php
 * Fungsi: Helper functions untuk mengecek permission berdasarkan role
 */

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Cek apakah user bisa manage barang (CRUD)
 * @return bool
 */
function can_manage_barang() {
    if (!isset($_SESSION['role'])) return false;
    return in_array($_SESSION['role'], ['cs', 'administrator']);
}

/**
 * Cek apakah user bisa manage transaksi (stok masuk/keluar)
 * @return bool
 */
function can_manage_transactions() {
    if (!isset($_SESSION['role'])) return false;
    return in_array($_SESSION['role'], ['cs', 'administrator']);
}

/**
 * Cek apakah user bisa manage users
 * @return bool
 */
function can_manage_users() {
    if (!isset($_SESSION['role'])) return false;
    return $_SESSION['role'] === 'administrator';
}

/**
 * Cek apakah user adalah view-only (owner)
 * @return bool
 */
function is_view_only() {
    if (!isset($_SESSION['role'])) return false;
    return $_SESSION['role'] === 'owner';
}

/**
 * Cek apakah user bisa akses halaman tertentu
 * @param string $page - nama halaman (barang, transaksi, users)
 * @return bool
 */
function can_access_page($page) {
    if (!isset($_SESSION['role'])) return false;
    
    switch($page) {
        case 'barang':
            return can_manage_barang();
        case 'transaksi':
            return can_manage_transactions();
        case 'users':
            return can_manage_users();
        case 'laporan':
        case 'logs':
        case 'dashboard':
            return true; // Semua role bisa akses
        default:
            return false;
    }
}

/**
 * Redirect jika user tidak punya akses
 * @param string $page - nama halaman
 */
function check_page_access($page) {
    if (!can_access_page($page)) {
        $_SESSION['error_message'] = 'Akses ditolak! Anda tidak memiliki izin untuk mengakses halaman ini.';
        header('Location: ../index.php');
        exit;
    }
}
?>
