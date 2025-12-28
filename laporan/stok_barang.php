<?php
/**
 * ========================================
 * LAPORAN STOK BARANG
 * ========================================
 * File: laporan/stok_barang.php
 * Fungsi: Menampilkan laporan stok barang real-time
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';
require_once '../config/stock_helper.php';

// Set variabel untuk template
$page_title = 'Laporan Stok Barang';
$page_icon = 'chart-bar';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '../index.php'],
    ['label' => 'Laporan Stok Barang']
];

// Filter kategori (jika ada)
$filter_kategori = isset($_GET['kategori']) ? escape($_GET['kategori']) : '';

// Query untuk mengambil data stok barang
$query = "SELECT * FROM barang WHERE 1=1";

if (!empty($filter_kategori)) {
    $query .= " AND kategori = '$filter_kategori'";
}

$query .= " ORDER BY nama_barang ASC";
$result = mysqli_query($conn, $query);

// Query untuk list kategori (untuk filter)
$query_kategori = "SELECT DISTINCT kategori FROM barang ORDER BY kategori ASC";
$result_kategori = mysqli_query($conn, $query_kategori);

// Hitung statistik
$total_jenis = mysqli_num_rows($result);
$total_stok = 0;
$stok_aman = 0;
$stok_menipis = 0;

// Reset pointer result
mysqli_data_seek($result, 0);
while ($row = mysqli_fetch_assoc($result)) {
    $total_stok += $row['stok'];
    if ($row['stok'] >= 10) {
        $stok_aman++;
    } else {
        $stok_menipis++;
    }
}
// Reset pointer lagi untuk digunakan di tabel
mysqli_data_seek($result, 0);

// Include header
include '../includes/header.php';
?>

<!-- Sidebar -->
<?php include '../includes/sidebar.php'; ?>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Navbar -->
    <?php include '../includes/navbar.php'; ?>
    
    <!-- Main Content -->
    <div class="container-fluid px-4">
        
        <!-- Statistik Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stat-card blue">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Total Jenis Barang</h6>
                        <h3 class="mb-0"><?= number_format($total_jenis) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card green">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Total Stok</h6>
                        <h3 class="mb-0"><?= number_format($total_stok) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card green">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Stok Aman (≥10)</h6>
                        <h3 class="mb-0"><?= number_format($stok_aman) ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stat-card red">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Stok Menipis (<10)</h6>
                        <h3 class="mb-0"><?= number_format($stok_menipis) ?></h3>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Card Laporan -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center no-print">
                <span>
                    <i class="fas fa-chart-bar me-2"></i>
                    Laporan Stok Barang
                    <?= !empty($filter_kategori) ? "- Kategori: $filter_kategori" : "" ?>
                </span>
                <div>
                    <button onclick="printLaporan()" class="btn btn-success btn-sm">
                        <i class="fas fa-print me-2"></i>Print
                    </button>
                    <button onclick="exportExcel()" class="btn btn-primary btn-sm">
                        <i class="fas fa-file-excel me-2"></i>Export Excel
                    </button>
                </div>
            </div>
            <div class="card-body">
                
                <!-- Filter -->
                <div class="row mb-3 no-print">
                    <div class="col-md-4">
                        <form method="GET" action="">
                            <div class="input-group">
                                <select name="kategori" class="form-select" onchange="this.form.submit()">
                                    <option value="">Semua Kategori</option>
                                    <?php while ($kat = mysqli_fetch_assoc($result_kategori)): ?>
                                        <option value="<?= $kat['kategori'] ?>" 
                                                <?= ($filter_kategori == $kat['kategori']) ? 'selected' : '' ?>>
                                            <?= $kat['kategori'] ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                                <?php if (!empty($filter_kategori)): ?>
                                    <a href="stok_barang.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4 offset-md-4 no-print">
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" 
                                   id="searchStokBarang" 
                                   class="form-control" 
                                   placeholder="Cari data..."
                                   autocomplete="off">
                        </div>
                    </div>
                </div>
                
                <!-- Tabel Laporan -->
                <div class="table-responsive" id="printArea">
                    <!-- Header untuk Print -->
                    <div class="print-header text-center mb-4" style="display:none;">
                        <h3>LAPORAN STOK BARANG</h3>
                        <p>Tanggal Cetak: <?= tgl_indo(date('Y-m-d')) ?></p>
                        <?php if (!empty($filter_kategori)): ?>
                            <p>Kategori: <?= $filter_kategori ?></p>
                        <?php endif; ?>
                        <hr>
                    </div>
                    
                    <table id="tableStokBarang" class="table table-hover table-striped table-bordered">
                        <thead class="table-primary">
                            <tr class="text-center">
                                <th width="5%" class="sortable">No</th>
                                <th width="12%" class="sortable">Kode Barang</th>
                                <th width="25%" class="sortable">Nama Barang</th>
                                <th width="15%" class="sortable">Kategori</th>
                                <th width="10%" class="sortable">Satuan</th>
                                <th width="12%" class="text-center sortable">Stok</th>
                                <th width="12%" class="text-center sortable">Status</th>
                                <th width="9%" class="text-center no-print">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (mysqli_num_rows($result) > 0):
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)): 
                                    // Get status menggunakan helper function
                                    $status_badge = render_stock_badge($row['stok']);
                            ?>
                                <tr class="text-center">
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= $row['kode_barang'] ?></strong></td>
                                    <td><?= $row['nama_barang'] ?></td>
                                    <td><?= $row['kategori'] ?></td>
                                    <td><?= $row['satuan'] ?></td>
                                    <td class="text-center" data-sort="<?= $row['stok'] ?>">
                                        <strong><?= number_format($row['stok']) ?></strong>
                                    </td>
                                    <td class="text-center"><?= $status_badge ?></td>
                                    <td class="text-center no-print">
                                        <a href="../barang/edit.php?id=<?= $row['id'] ?>" 
                                           class="btn btn-sm btn-warning"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-3x mb-2"></i><br>
                                        Tidak ada data
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    
                    <!-- Footer untuk Print -->
                    <div class="print-footer mt-4" style="display:none;">
                        <div class="row">
                            <div class="col-6">
                                <p>Total Jenis Barang: <strong><?= $total_jenis ?></strong></p>
                                <p>Total Stok: <strong><?= number_format($total_stok) ?></strong></p>
                            </div>
                            <div class="col-6 text-end">
                                <p>Dicetak oleh: <?= $_SESSION['nama_lengkap'] ?></p>
                                <p>Tanggal: <?= date('d/m/Y H:i') ?> WIB</p>
                            </div>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        
    </div>
</div>

<!-- CSS untuk Print -->
<style>
@media print {
    /* Hide sidebar and navbar */
    .sidebar,
    .navbar,
    nav.navbar,
    .content-wrapper > nav {
        display: none !important;
    }
    
    /* Reset body and main containers */
    body {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .content-wrapper {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .container-fluid {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    /* Hide stat cards and other non-print elements */
    .stat-card,
    .card:not(:has(#printArea)),
    .row:has(.stat-card) {
        display: none !important;
    }
    
    /* Show only print area */
    #printArea {
        display: block !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    
    .no-print {
        display: none !important;
    }
    .sort-icon {
        display: none !important;
    }
    .print-header, .print-footer {
        display: block !important;
    }
    .table {
        font-size: 12px;
    }
    .card {
        border: none;
        box-shadow: none;
    }
    
    /* Hide scrollbars */
    .table-responsive {
        overflow: visible !important;
    }
    
    body {
        overflow: visible !important;
    }
    
    /* Ensure table fits page width */
    .table {
        width: 100% !important;
    }
    
    /* Page settings */
    @page {
        margin: 1cm;
        size: A4;
    }
}
</style>

<!-- JavaScript -->
<script>
function printLaporan() {
    window.print();
}

function exportExcel() {
    // Redirect ke file export Excel
    window.location.href = 'export_stok_excel.php<?= !empty($filter_kategori) ? "?kategori=$filter_kategori" : "" ?>';
}
</script>

<!-- Include Table Utils -->
<script src="../assets/js/table-utils.js"></script>
<script>
    // Initialize table search and sort
    document.addEventListener('DOMContentLoaded', function() {
        initTable('tableStokBarang', 'searchStokBarang');
    });
</script>

<?php include '../includes/footer.php'; ?>