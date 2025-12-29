<?php
/**
 * ========================================
 * LOG ACTIVITY HELPER
 * ========================================
 * File: config/log_helper.php
 * Fungsi: Helper function untuk log aktivitas user
 */

// Set timezone ke Indonesia (WIB)
date_default_timezone_set('Asia/Jakarta');

/**
 * Log aktivitas user ke database
 * 
 * @param int $user_id - ID user yang melakukan aktivitas
 * @param string $username - Username user
 * @param string $action - Jenis aksi (LOGIN, LOGOUT, CREATE, UPDATE, DELETE, dll)
 * @param string $module - Module sistem (AUTH, BARANG, STOK_MASUK, STOK_KELUAR, PROFILE, LAPORAN)
 * @param string $description - Deskripsi detail aktivitas
 * @return bool - True jika berhasil, false jika gagal
 */
function log_activity($user_id, $username, $action, $module, $description) {
    global $conn;
    
    // Get IP address
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    
    // Get User Agent
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
    
    // Prepare query
    $query = "INSERT INTO activity_logs 
              (user_id, username, action, module, description, ip_address, user_agent) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    // Prepare statement
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "issssss", 
            $user_id, 
            $username, 
            $action, 
            $module, 
            $description, 
            $ip_address, 
            $user_agent
        );
        
        $result = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        
        return $result;
    }
    
    return false;
}

/**
 * Get daftar action yang tersedia
 */
function get_log_actions() {
    return [
        'LOGIN' => 'Login',
        'LOGIN_FAILED' => 'Login Gagal',
        'LOGOUT' => 'Logout',
        'CREATE' => 'Tambah Data',
        'UPDATE' => 'Ubah Data',
        'DELETE' => 'Hapus Data',
        'VIEW' => 'Lihat Data',
        'EXPORT' => 'Export Data'
    ];
}

/**
 * Get daftar module yang tersedia
 */
function get_log_modules() {
    return [
        'AUTH' => 'Authentication',
        'BARANG' => 'Master Barang',
        'STOK_MASUK' => 'Stok Masuk',
        'STOK_KELUAR' => 'Stok Keluar',
        'PROFILE' => 'Profile',
        'LAPORAN' => 'Laporan'
    ];
}
?>
