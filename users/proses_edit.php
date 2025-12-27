<?php
/**
 * ========================================
 * PROSES EDIT USER
 * ========================================
 * File: users/proses_edit.php
 * Fungsi: Memproses update data user
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';
require_once '../config/check_admin.php';
require_once '../config/log_helper.php';

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil dan sanitasi input
    $user_id = (int)$_POST['id'];
    $username = escape($_POST['username']);
    $nama_lengkap = escape($_POST['nama_lengkap']);
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $password_confirm = isset($_POST['password_confirm']) ? $_POST['password_confirm'] : '';
    $role = escape($_POST['role']);
    
    // Validasi input
    if (empty($username) || empty($nama_lengkap) || empty($role)) {
        $_SESSION['error_message'] = 'Username, nama lengkap, dan role harus diisi!';
        header("Location: edit.php?id=$user_id");
        exit;
    }
    
    // Validasi role
    $valid_roles = ['owner', 'administrator', 'cs'];
    if (!in_array($role, $valid_roles)) {
        $_SESSION['error_message'] = 'Role tidak valid!';
        header("Location: edit.php?id=$user_id");
        exit;
    }
    
    // Cek apakah username sudah digunakan user lain
    $check_query = "SELECT id FROM users WHERE username = '$username' AND id != $user_id LIMIT 1";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error_message'] = 'Username sudah digunakan user lain!';
        header("Location: edit.php?id=$user_id");
        exit;
    }
    
    // Prepare update query
    $update_parts = [
        "username = '$username'",
        "nama_lengkap = '$nama_lengkap'",
        "role = '$role'"
    ];
    
    // Jika password diisi, update juga password
    if (!empty($password)) {
        // Validasi password match
        if ($password !== $password_confirm) {
            $_SESSION['error_message'] = 'Password dan konfirmasi password tidak cocok!';
            header("Location: edit.php?id=$user_id");
            exit;
        }
        
        // Validasi panjang password
        if (strlen($password) < 6) {
            $_SESSION['error_message'] = 'Password minimal 6 karakter!';
            header("Location: edit.php?id=$user_id");
            exit;
        }
        
        $password_hash = md5($password);
        $update_parts[] = "password = '$password_hash'";
    }
    
    // Build update query
    $update_query = "UPDATE users SET " . implode(', ', $update_parts) . " WHERE id = $user_id LIMIT 1";
    
    if (mysqli_query($conn, $update_query)) {
        // Berhasil
        log_activity(
            $_SESSION['user_id'], 
            $_SESSION['username'], 
            'USER_UPDATE', 
            'USER_MANAGEMENT', 
            "Mengupdate data user: $username (Role: $role)"
        );
        
        // Jika user mengedit dirinya sendiri dan mengubah role, update session
        if ($user_id == $_SESSION['user_id']) {
            $_SESSION['role'] = $role;
            $_SESSION['nama_lengkap'] = $nama_lengkap;
            $_SESSION['username'] = $username;
        }
        
        $_SESSION['success_message'] = "Data user $username berhasil diupdate!";
        header('Location: index.php');
        exit;
    } else {
        // Gagal
        $_SESSION['error_message'] = 'Gagal mengupdate user: ' . mysqli_error($conn);
        header("Location: edit.php?id=$user_id");
        exit;
    }
    
} else {
    // Jika diakses langsung tanpa POST
    header('Location: index.php');
    exit;
}
?>
