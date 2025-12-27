<?php
/**
 * ========================================
 * PROSES LOGOUT
 * ========================================
 * File: auth/logout.php
 * Fungsi: Menghapus session dan logout user
 */

session_start();

require_once '../config/koneksi.php';
require_once '../config/log_helper.php';

// Log logout before destroying session
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    log_activity(
        $_SESSION['user_id'], 
        $_SESSION['username'], 
        'LOGOUT', 
        'AUTH', 
        'User logout dari sistem'
    );
}

// Hapus semua session
session_unset();

// Hancurkan session
session_destroy();

// Redirect ke halaman login
header('Location: login.php');
exit;
?>