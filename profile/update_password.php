<?php
/**
 * ========================================
 * PROSES UPDATE PASSWORD
 * ========================================
 * File: profile/update_password.php
 * Fungsi: Memproses update password administrator
 */

session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    header('Location: ../auth/login.php?error=2');
    exit;
}

require_once '../config/koneksi.php';

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil input
    $old_password = escape($_POST['old_password']);
    $new_password = escape($_POST['new_password']);
    $confirm_password = escape($_POST['confirm_password']);
    $user_id = $_SESSION['user_id'];
    
    // Validasi: Password baru minimal 6 karakter
    if (strlen($new_password) < 6) {
        header('Location: settings.php?error=password_short');
        exit;
    }
    
    // Validasi: Password baru dan konfirmasi harus match
    if ($new_password !== $confirm_password) {
        header('Location: settings.php?error=password_mismatch');
        exit;
    }
    
    // Ambil password lama dari database
    $query = "SELECT password FROM users WHERE id = '$user_id' LIMIT 1";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);
    
    // Validasi: Cek password lama benar
    $old_password_hash = md5($old_password);
    if ($old_password_hash !== $user['password']) {
        header('Location: settings.php?error=password_wrong');
        exit;
    }
    
    // Hash password baru dengan MD5 (konsisten dengan sistem)
    $new_password_hash = md5($new_password);
    
    // Update password di database
    $update_query = "UPDATE users SET password = '$new_password_hash' WHERE id = '$user_id'";
    
    if (mysqli_query($conn, $update_query)) {
        // Redirect dengan pesan sukses
        header('Location: settings.php?success=password');
        exit;
    } else {
        // Error database
        header('Location: settings.php?error=database');
        exit;
    }
    
} else {
    // Jika akses langsung tanpa submit form
    header('Location: settings.php');
    exit;
}
?>
