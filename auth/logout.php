<?php
/**
 * ========================================
 * PROSES LOGOUT
 * ========================================
 * File: auth/logout.php
 * Fungsi: Menghapus session dan logout user
 */

session_start();

// Hapus semua session
session_unset();

// Hancurkan session
session_destroy();

// Redirect ke halaman login
header('Location: login.php');
exit;
?>