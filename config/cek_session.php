<?php
/**
 * ========================================
 * CEK SESSION
 * ========================================
 * File: config/cek_session.php
 * Fungsi: Memvalidasi session user dan proteksi halaman
 * Cara pakai: Include file ini di setiap halaman yang butuh login
 */

// Pastikan session sudah dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    // Jika belum login, redirect ke halaman login
    header('Location: ../auth/login.php?error=2');
    exit;
}

// Optional: Cek timeout session (30 menit)
$timeout_duration = 1800; // 30 menit dalam detik
if (isset($_SESSION['last_activity']) && 
    (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Session timeout, logout otomatis
    session_unset();
    session_destroy();
    header('Location: ../auth/login.php?error=3');
    exit;
}

// Update waktu aktivitas terakhir
$_SESSION['last_activity'] = time();
?>