<?php
/**
 * ========================================
 * PROSES UPDATE ACCOUNT (USERNAME & PASSWORD)
 * ========================================
 * File: profile/update_account.php
 * Fungsi: Memproses update username dan/atau password secara bersamaan
 */

session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    header('Location: ../auth/login.php?error=2');
    exit;
}

require_once '../config/koneksi.php';
require_once '../config/log_helper.php';

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil input
    $new_username = isset($_POST['new_username']) ? trim(escape($_POST['new_username'])) : '';
    $old_password = isset($_POST['old_password']) ? trim(escape($_POST['old_password'])) : '';
    $new_password = isset($_POST['new_password']) ? trim(escape($_POST['new_password'])) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim(escape($_POST['confirm_password'])) : '';
    $user_id = $_SESSION['user_id'];
    
    // Flags untuk tracking perubahan
    $update_username = false;
    $update_password = false;
    $will_logout = false;
    
    // ===== VALIDASI USERNAME =====
    if (!empty($new_username)) {
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
        
        $update_username = true;
        $will_logout = true; // Logout jika ganti username
    }
    
    // ===== VALIDASI PASSWORD =====
    if (!empty($new_password) || !empty($old_password)) {
        // Jika salah satu field password diisi, semua harus diisi
        if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
            header('Location: settings.php?error=password_incomplete');
            exit;
        }
        
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
        
        $update_password = true;
        $will_logout = true; // Logout jika ganti password
    }
    
    // Cek apakah ada perubahan
    if (!$update_username && !$update_password) {
        header('Location: settings.php?error=no_changes');
        exit;
    }
    
    // ===== PROSES UPDATE =====
    $updates = [];
    $log_messages = [];
    
    if ($update_username) {
        $updates[] = "username = '$new_username'";
        $log_messages[] = "Mengubah username dari '{$_SESSION['username']}' menjadi '$new_username'";
    }
    
    if ($update_password) {
        $new_password_hash = md5($new_password);
        $updates[] = "password = '$new_password_hash'";
        $log_messages[] = "Mengubah password akun";
    }
    
    // Gabungkan update query
    $update_query = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = '$user_id'";
    
    if (mysqli_query($conn, $update_query)) {
        // Log activity
        $log_message = implode(' dan ', $log_messages);
        log_activity($user_id, $_SESSION['username'], 'UPDATE_ACCOUNT', 'PROFILE', $log_message);
        
        if ($will_logout) {
            // Logout otomatis - destroy session
            session_unset();
            session_destroy();
            
            // Redirect ke login dengan pesan sukses
            if ($update_username && $update_password) {
                header('Location: ../auth/login.php?success=account_updated');
            } elseif ($update_username) {
                header('Location: ../auth/login.php?success=username_updated');
            } else {
                header('Location: ../auth/login.php?success=password_updated');
            }
        } else {
            // Tidak logout (tidak seharusnya terjadi, tapi untuk safety)
            header('Location: settings.php?success=updated');
        }
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
