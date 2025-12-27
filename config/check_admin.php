<?php
/**
 * ========================================
 * CHECK ADMIN MIDDLEWARE
 * ========================================
 * File: config/check_admin.php
 * Fungsi: Memastikan hanya administrator yang bisa mengakses halaman tertentu
 */

// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header('Location: ../auth/login.php');
    exit;
}

// Cek apakah user memiliki role administrator
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'administrator') {
    // User bukan administrator, redirect ke dashboard dengan pesan error
    $_SESSION['error_message'] = 'Akses ditolak! Anda tidak memiliki izin untuk mengakses halaman ini.';
    header('Location: ../index.php');
    exit;
}

// Jika sampai sini, user adalah administrator dan boleh melanjutkan
?>
