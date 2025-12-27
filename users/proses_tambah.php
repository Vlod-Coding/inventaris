<?php
/**
 * ========================================
 * PROSES TAMBAH USER
 * ========================================
 * File: users/proses_tambah.php
 * Fungsi: Memproses penambahan user baru
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';
require_once '../config/check_admin.php';
require_once '../config/log_helper.php';

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil dan sanitasi input
    $username = escape($_POST['username']);
    $nama_lengkap = escape($_POST['nama_lengkap']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $role = escape($_POST['role']);
    
    // Validasi input
    if (empty($username) || empty($nama_lengkap) || empty($password) || empty($role)) {
        $_SESSION['error_message'] = 'Semua field harus diisi!';
        header('Location: tambah.php');
        exit;
    }
    
    // Validasi password match
    if ($password !== $password_confirm) {
        $_SESSION['error_message'] = 'Password dan konfirmasi password tidak cocok!';
        header('Location: tambah.php');
        exit;
    }
    
    // Validasi panjang password
    if (strlen($password) < 6) {
        $_SESSION['error_message'] = 'Password minimal 6 karakter!';
        header('Location: tambah.php');
        exit;
    }
    
    // Validasi role
    $valid_roles = ['owner', 'administrator', 'cs'];
    if (!in_array($role, $valid_roles)) {
        $_SESSION['error_message'] = 'Role tidak valid!';
        header('Location: tambah.php');
        exit;
    }
    
    // Cek apakah username sudah ada
    $check_query = "SELECT id FROM users WHERE username = '$username' LIMIT 1";
    $check_result = mysqli_query($conn, $check_query);
    
    if (mysqli_num_rows($check_result) > 0) {
        $_SESSION['error_message'] = 'Username sudah digunakan! Silakan pilih username lain.';
        header('Location: tambah.php');
        exit;
    }
    
    // Hash password dengan MD5 (sesuai sistem yang ada)
    $password_hash = md5($password);
    
    // Insert user baru
    $insert_query = "INSERT INTO users (username, nama_lengkap, password, role, created_at) 
                     VALUES ('$username', '$nama_lengkap', '$password_hash', '$role', NOW())";
    
    if (mysqli_query($conn, $insert_query)) {
        // Berhasil
        $new_user_id = mysqli_insert_id($conn);
        
        // Log activity
        log_activity(
            $_SESSION['user_id'], 
            $_SESSION['username'], 
            'USER_CREATE', 
            'USER_MANAGEMENT', 
            "Menambahkan user baru: $username (Role: $role)"
        );
        
        $_SESSION['success_message'] = "User $username berhasil ditambahkan!";
        header('Location: index.php');
        exit;
    } else {
        // Gagal
        $_SESSION['error_message'] = 'Gagal menambahkan user: ' . mysqli_error($conn);
        header('Location: tambah.php');
        exit;
    }
    
} else {
    // Jika diakses langsung tanpa POST
    header('Location: tambah.php');
    exit;
}
?>
