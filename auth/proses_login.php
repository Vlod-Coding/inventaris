<?php
/**
 * ========================================
 * PROSES LOGIN
 * ========================================
 * File: auth/proses_login.php
 * Fungsi: Memproses autentikasi user
 */

session_start();
require_once '../config/koneksi.php';

// Cek apakah form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Ambil dan sanitasi input
    $username = escape($_POST['username']);
    $password = escape($_POST['password']);
    
    // Enkripsi password dengan MD5 (sesuaikan dengan database)
    $password_hash = md5($password);
    
    // Query untuk cek user di database
    $query = "SELECT * FROM users 
              WHERE username = '$username' 
              AND password = '$password_hash'
              LIMIT 1";
    
    $result = mysqli_query($conn, $query);
    
    // Cek apakah user ditemukan
    if (mysqli_num_rows($result) == 1) {
        // User ditemukan, ambil data user
        $user = mysqli_fetch_assoc($result);
        
        // Simpan data user ke session
        $_SESSION['login'] = true;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
        
        // Catat waktu login
        $_SESSION['login_time'] = date('Y-m-d H:i:s');
        
        // Redirect ke dashboard
        header('Location: ../index.php');
        exit;
        
    } else {
        // User tidak ditemukan atau password salah
        header('Location: login.php?error=1');
        exit;
    }
    
} else {
    // Jika akses langsung tanpa submit form
    header('Location: login.php');
    exit;
}
?>