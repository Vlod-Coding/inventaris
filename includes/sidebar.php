<?php
/**
 * ========================================
 * SIDEBAR NAVIGATION
 * ========================================
 * File: includes/sidebar.php
 * Fungsi: Menu navigasi sidebar
 */

// Tentukan halaman aktif untuk highlight menu
$current_page = basename($_SERVER['PHP_SELF']);
$current_uri = $_SERVER['REQUEST_URI']; // Path lengkap untuk deteksi folder
?>

<div class="sidebar">
    <!-- Logo -->
    <div class="logo">
        <i class="fas fa-box-open"></i>
        <h5 class="mb-0">Sistem Inventaris</h5>
        <small>Manajemen Stok Barang</small>
    </div>
    
    <!-- User Info -->
    <div class="user-info text-center py-3 border-bottom border-white border-opacity-10">
        <i class="fas fa-user-circle fa-3x mb-2"></i>
        <p class="mb-0"><strong><?= isset($_SESSION['username']) ? $_SESSION['username'] : 'User' ?></strong></p>
        <small class="text-white-50">
            <?php 
            if (isset($_SESSION['role'])) {
                switch($_SESSION['role']) {
                    case 'owner':
                        echo 'Owner';
                        break;
                    case 'administrator':
                        echo 'Administrator';
                        break;
                    case 'cs':
                        echo 'Customer Service';
                        break;
                    default:
                        echo ucfirst($_SESSION['role']);
                }
            } else {
                echo 'User';
            }
            ?>
        </small>
    </div>
    
    <!-- Navigation Menu -->
    <nav class="nav flex-column mt-3">
        <!-- Dashboard -->
        <a href="/inventaris/index.php" 
           class="nav-link <?= ($current_page == 'index.php' && strpos($current_uri, '/barang/') === false && strpos($current_uri, '/transaksi/') === false && strpos($current_uri, '/laporan/') === false && strpos($current_uri, '/logs/') === false) ? 'active' : '' ?>">
            <i class="fas fa-home"></i>
            Dashboard
        </a>
        
        <!-- Data Barang (CS & Admin only) -->
        <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['cs', 'administrator'])): ?>
        <a href="/inventaris/barang/index.php" 
           class="nav-link <?= (strpos($current_uri, '/barang/') !== false) ? 'active' : '' ?>">
            <i class="fas fa-boxes"></i>
            Data Barang
        </a>
        <?php endif; ?>
        
        <!-- Stok Masuk (CS & Admin only) -->
        <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['cs', 'administrator'])): ?>
        <a href="/inventaris/transaksi/stok_masuk.php" 
           class="nav-link <?= ($current_page == 'stok_masuk.php') ? 'active' : '' ?>">
            <i class="fas fa-arrow-down"></i>
            Stok Masuk
        </a>
        <?php endif; ?>
        
        <!-- Stok Keluar (CS & Admin only) -->
        <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['cs', 'administrator'])): ?>
        <a href="/inventaris/transaksi/stok_keluar.php" 
           class="nav-link <?= ($current_page == 'stok_keluar.php') ? 'active' : '' ?>">
            <i class="fas fa-arrow-up"></i>
            Stok Keluar
        </a>
        <?php endif; ?>
        
        <!-- Divider (only if CS or Admin) -->
        <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['cs', 'administrator'])): ?>
        <hr class="border-white border-opacity-10 my-2">
        <?php endif; ?>
        
        <!-- Laporan Stok Barang (All roles) -->
        <a href="/inventaris/laporan/stok_barang.php" 
           class="nav-link <?= ($current_page == 'stok_barang.php') ? 'active' : '' ?>">
            <i class="fas fa-chart-bar"></i>
            Laporan Stok
        </a>
        
        <!-- Laporan Transaksi Masuk (All roles) -->
        <a href="/inventaris/laporan/transaksi_masuk.php" 
           class="nav-link <?= ($current_page == 'transaksi_masuk.php') ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i>
            Laporan Masuk
        </a>
        
        <!-- Laporan Transaksi Keluar (All roles) -->
        <a href="/inventaris/laporan/transaksi_keluar.php" 
           class="nav-link <?= ($current_page == 'transaksi_keluar.php') ? 'active' : '' ?>">
            <i class="fas fa-file-invoice"></i>
            Laporan Keluar
        </a>
        
        <!-- Divider -->
        <hr class="border-white border-opacity-10 my-2">
        
        <!-- Kelola User (Admin Only) -->
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'administrator'): ?>
        <a href="/inventaris/users/index.php" 
           class="nav-link <?= (strpos($current_uri, '/users/') !== false) ? 'active' : '' ?>">
            <i class="fas fa-users-cog"></i>
            Kelola User
        </a>
        
        <!-- Divider -->
        <hr class="border-white border-opacity-10 my-2">
        <?php endif; ?>
        
        <!-- Log Aktivitas (All roles) -->
        <a href="/inventaris/logs/index.php" 
           class="nav-link <?= (strpos($current_uri, '/logs/') !== false) ? 'active' : '' ?>">
            <i class="fas fa-history"></i>
            Log Aktivitas
        </a>
    </nav>
</div>