<?php
/**
 * ========================================
 * DASHBOARD UTAMA
 * ========================================
 * File: index.php
 * Fungsi: Halaman dashboard dengan statistik
 */

session_start();
require_once 'config/koneksi.php';
require_once 'config/cek_session.php';

// Set variabel untuk template
$page_title = 'Dashboard';
$page_icon = 'home';

// Query untuk mendapatkan statistik

// 1. Total Barang
$query_total_barang = "SELECT COUNT(*) as total FROM barang";
$result_barang = mysqli_query($conn, $query_total_barang);
$total_barang = mysqli_fetch_assoc($result_barang)['total'];

// 2. Total Stok Keseluruhan
$query_total_stok = "SELECT SUM(stok) as total FROM barang";
$result_stok = mysqli_query($conn, $query_total_stok);
$total_stok = mysqli_fetch_assoc($result_stok)['total'] ?? 0;

// 3. Total Transaksi Masuk (Hari Ini)
$query_masuk_hari_ini = "SELECT COUNT(*) as total FROM stok_masuk 
                         WHERE DATE(tanggal) = CURDATE()";
$result_masuk = mysqli_query($conn, $query_masuk_hari_ini);
$transaksi_masuk_hari_ini = mysqli_fetch_assoc($result_masuk)['total'];

// 4. Total Transaksi Keluar (Hari Ini)
$query_keluar_hari_ini = "SELECT COUNT(*) as total FROM stok_keluar 
                          WHERE DATE(tanggal) = CURDATE()";
$result_keluar = mysqli_query($conn, $query_keluar_hari_ini);
$transaksi_keluar_hari_ini = mysqli_fetch_assoc($result_keluar)['total'];

// 5. Barang dengan Stok Menipis (Stok < 10)
$query_stok_menipis = "SELECT * FROM barang WHERE stok < 10 ORDER BY stok ASC LIMIT 5";
$result_stok_menipis = mysqli_query($conn, $query_stok_menipis);

// 6. Transaksi Terakhir (5 transaksi terbaru)
$query_transaksi_terakhir = "
    SELECT 'Masuk' as jenis, sm.tanggal, b.nama_barang, sm.jumlah, sm.created_at
    FROM stok_masuk sm
    JOIN barang b ON sm.barang_id = b.id
    UNION ALL
    SELECT 'Keluar' as jenis, sk.tanggal, b.nama_barang, sk.jumlah, sk.created_at
    FROM stok_keluar sk
    JOIN barang b ON sk.barang_id = b.id
    ORDER BY created_at DESC
    LIMIT 5
";
$result_transaksi = mysqli_query($conn, $query_transaksi_terakhir);

// Include header
include 'includes/header.php';
?>

<!-- Sidebar -->
<?php include 'includes/sidebar.php'; ?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Navbar -->
    <?php include 'includes/navbar.php'; ?>
    
    <!-- Main Content -->
    <div class="container-fluid px-4">
        
        <!-- Statistics Cards -->
        <div class="row">
            <!-- Total Barang -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card blue">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Barang</h6>
                                <h2 class="mb-0"><?= number_format($total_barang) ?></h2>
                            </div>
                            <div class="text-primary">
                                <i class="fas fa-box fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Total Stok -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card green">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Stok</h6>
                                <h2 class="mb-0"><?= number_format($total_stok) ?></h2>
                            </div>
                            <div class="text-success">
                                <i class="fas fa-cubes fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Transaksi Masuk Hari Ini -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card orange">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Stok Masuk (Hari Ini)</h6>
                                <h2 class="mb-0"><?= $transaksi_masuk_hari_ini ?></h2>
                            </div>
                            <div class="text-warning">
                                <i class="fas fa-arrow-down fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Transaksi Keluar Hari Ini -->
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card red">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Stok Keluar (Hari Ini)</h6>
                                <h2 class="mb-0"><?= $transaksi_keluar_hari_ini ?></h2>
                            </div>
                            <div class="text-danger">
                                <i class="fas fa-arrow-up fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <!-- Barang Stok Menipis -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Stok Menipis (< 10 unit)
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($result_stok_menipis) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr >
                                            <th>Kode</th>
                                            <th class="text-center">Nama Barang</th>
                                            <th class="text-center">Stok</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result_stok_menipis)): ?>
                                            <tr>
                                                <td><?= $row['kode_barang'] ?></td>
                                                <td class="text-center"><?= $row['nama_barang'] ?></td>
                                                <td class="text-center">
                                                    <span class="badge bg-danger">
                                                        <?= $row['stok'] ?> <?= $row['satuan'] ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-check-circle me-2"></i>
                                Semua barang memiliki stok yang cukup
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Transaksi Terakhir -->
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-history me-2"></i>
                        Transaksi Terakhir
                    </div>
                    <div class="card-body">
                        <?php if (mysqli_num_rows($result_transaksi) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Jenis</th>
                                            <th class="text-center">Barang</th>
                                            <th class="text-center">Jumlah</th>
                                            <th class="text-center">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = mysqli_fetch_assoc($result_transaksi)): ?>
                                            <tr>
                                                <td>
                                                    <?php if ($row['jenis'] == 'Masuk'): ?>
                                                        <span class="badge bg-success">
                                                            <i class="fas fa-arrow-down"></i> Masuk
                                                        </span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">
                                                            <i class="fas fa-arrow-up"></i> Keluar
                                                        </span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center"><?= $row['nama_barang'] ?></td>
                                                <td class="text-center"><?= $row['jumlah'] ?></td>
                                                <td class="text-center"><?= tgl_indo($row['tanggal']) ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                Belum ada transaksi
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<?php include 'includes/footer.php'; ?>