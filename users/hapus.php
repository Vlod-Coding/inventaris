<?php
/**
 * ========================================
 * HAPUS USER
 * ========================================
 * File: users/hapus.php
 * Fungsi: Menghapus user dari database
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';
require_once '../config/check_admin.php';
require_once '../config/log_helper.php';

// Cek apakah ada parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = 'ID user tidak valid!';
    header('Location: index.php');
    exit;
}

$user_id = (int)$_GET['id'];

// Ambil data user yang akan dihapus
$query = "SELECT username, role FROM users WHERE id = $user_id LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['error_message'] = 'User tidak ditemukan!';
    header('Location: index.php');
    exit;
}

$user_data = mysqli_fetch_assoc($result);

// Cek apakah user yang akan dihapus adalah administrator
if ($user_data['role'] === 'administrator') {
    $_SESSION['error_message'] = 'User dengan role Administrator tidak dapat dihapus!';
    header('Location: index.php');
    exit;
}

// Cek apakah user mencoba menghapus dirinya sendiri
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['error_message'] = 'Anda tidak dapat menghapus akun Anda sendiri!';
    header('Location: index.php');
    exit;
}

// Hapus user
$delete_query = "DELETE FROM users WHERE id = $user_id LIMIT 1";

if (mysqli_query($conn, $delete_query)) {
    // Berhasil
    log_activity(
        $_SESSION['user_id'], 
        $_SESSION['username'], 
        'USER_DELETE', 
        'USER_MANAGEMENT', 
        "Menghapus user: {$user_data['username']} (Role: {$user_data['role']})"
    );
    
    $_SESSION['success_message'] = "User {$user_data['username']} berhasil dihapus!";
} else {
    // Gagal
    $_SESSION['error_message'] = 'Gagal menghapus user: ' . mysqli_error($conn);
}

header('Location: index.php');
exit;
?>
