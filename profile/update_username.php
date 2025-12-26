<?php
/**
 * ========================================
 * PROSES UPDATE USERNAME
 * ========================================
 * File: profile/update_username.php
 * Fungsi: Memproses update username administrator
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
    $new_username = escape($_POST['username']);
    $user_id = $_SESSION['user_id'];
    
    // Validasi: Username tidak boleh kosong
    if (empty($new_username)) {
        header('Location: settings.php?error=username_empty');
        exit;
    }
    
    // Validasi: Username minimal 3 karakter
    if (strlen($new_username) < 3) {
        header('Location: settings.php?error=username_short');
        exit;
    }
    
    // Cek apakah username sudah digunakan user lain
    $check_query = "SELECT * FROM users WHERE username = '$new_username' AND id != '$user_id' LIMIT 1";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        header('Location: settings.php?error=username_exists');
        exit;
    }
    
    // Update username di database
    $update_query = "UPDATE users SET username = '$new_username' WHERE id = '$user_id'";
    
    if (mysqli_query($conn, $update_query)) {
        // Update session
        $_SESSION['username'] = $new_username;
        
        // Redirect dengan pesan sukses
        header('Location: settings.php?success=username');
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
