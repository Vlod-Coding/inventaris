<?php
/**
 * ========================================
 * LAPORAN TRANSAKSI KELUAR
 * ========================================
 * File: laporan/transaksi_keluar.php
 * Fungsi: Menampilkan laporan transaksi stok keluar dengan filter periode
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';

// Set variabel untuk template
$page_title = 'Laporan Transaksi Keluar';
$page_icon = 'file-invoice';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '../index.php'],
    ['label' => 'Laporan Transaksi Keluar']
];

// Filter tanggal
$tanggal_awal = isset($_GET['tanggal_awal']) ? escape($_GET['tanggal_awal']) : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? escape($_GET['tanggal_akhir']) : date('Y-m-d');

// Query untuk mengambil data transaksi keluar
$query = "SELECT sk.*, b.kode_barang, b.nama_barang, b.kategori, b.satuan
          FROM stok_keluar sk
          JOIN barang b ON sk.barang_id = b.id
          WHERE sk.tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
          ORDER BY sk.tanggal DESC, sk.created_at DESC";

$result = mysqli_query($conn, $query);

// Hitung statistik
$total_transaksi = mysqli_num_rows($result);
$total_item_keluar = 0;

// Reset pointer untuk hitung total
mysqli_data_seek($result, 0);
while ($row = mysqli_fetch_assoc($result)) {
    $total_item_keluar += $row['jumlah'];
}
// Reset pointer lagi
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
            <div class="col-md-6">
                <div class="card stat-card blue">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Total Transaksi</h6>
                        <h3 class="mb-0"><?= number_format($total_transaksi) ?></h3>
                        <small class="text-muted">
                            <?= tgl_indo($tanggal_awal) ?> - <?= tgl_indo($tanggal_akhir) ?>
                        </small>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card stat-card red">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Total Item Keluar</h6>
                        <h3 class="mb-0"><?= number_format($total_item_keluar) ?></h3>
                        <small class="text-muted">Jumlah keseluruhan barang keluar</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Card Laporan -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center no-print">
                <span>
                    <i class="fas fa-file-invoice me-2"></i>
                    Laporan Transaksi Stok Keluar
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
                
                <!-- Filter Periode -->
                <form method="GET" action="" class="no-print">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Awal</label>
                            <input type="date" 
                                   name="tanggal_awal" 
                                   class="form-control"
                                   value="<?= $tanggal_awal ?>"
                                   max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Tanggal Akhir</label>
                            <input type="date" 
                                   name="tanggal_akhir" 
                                   class="form-control"
                                   value="<?= $tanggal_akhir ?>"
                                   max="<?= date('Y-m-d') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Filter
                                </button>
                                <a href="transaksi_keluar.php" class="btn btn-secondary">
                                    <i class="fas fa-redo me-2"></i>Reset
                                </a>
                            </div>
                        </div>
                        <div class="col-md-3 no-print">
                            <label class="form-label">&nbsp;</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       id="searchLaporanKeluar" 
                                       class="form-control" 
                                       placeholder="Cari data..."
                                       autocomplete="off">
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- Tabel Laporan -->
                <div class="table-responsive" id="printArea">
                    <!-- Header untuk Print -->
                    <div class="print-header text-center mb-4" style="display:none;">
                        <h3>LAPORAN TRANSAKSI STOK KELUAR</h3>
                        <p>Periode: <?= tgl_indo($tanggal_awal) ?> s/d <?= tgl_indo($tanggal_akhir) ?></p>
                        <hr>
                    </div>
                    
                    <table id="tableLaporanKeluar" class="table table-hover table-striped table-bordered">
                        <thead class="table-danger">
                            <tr class="text-center">
                                <th width="5%" class="sortable">No</th>
                                <th width="10%" class="sortable">Tanggal</th>
                                <th width="12%" class="sortable">Kode Barang</th>
                                <th width="20%" class="sortable">Nama Barang</th>
                                <th width="12%" class="sortable">Kategori</th>
                                <th width="10%" class="text-center sortable">Jumlah</th>
                                <th width="15%" class="sortable">Penanggung Jawab</th>
                                <th width="16%">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (mysqli_num_rows($result) > 0):
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)): 
                            ?>
                                <tr class="text-center">
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                    <td><strong><?= $row['kode_barang'] ?></strong></td>
                                    <td><?= $row['nama_barang'] ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= $row['kategori'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center" data-sort="<?= $row['jumlah'] ?>">
                                        <span class="badge bg-danger">
                                            -<?= number_format($row['jumlah']) ?> <?= $row['satuan'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        if (!empty($row['penanggung_jawab'])) {
                                            echo $row['penanggung_jawab'];
                                        } else {
                                            echo '<span class="text-muted">-</span>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        if (!empty($row['keterangan'])) {
                                            echo strlen($row['keterangan']) > 50 
                                                ? substr($row['keterangan'], 0, 50) . '...' 
                                                : $row['keterangan'];
                                        } else {
                                            echo '<span class="text-muted">-</span>';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <tr>
                                    <td colspan="8" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-3x mb-2"></i><br>
                                        Tidak ada transaksi pada periode ini
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    
                    <!-- Footer untuk Print -->
                    <div class="print-footer mt-4" style="display:none;">
                        <div class="row">
                            <div class="col-6">
                                <p>Total Transaksi: <strong><?= $total_transaksi ?></strong></p>
                                <p>Total Item Keluar: <strong><?= number_format($total_item_keluar) ?></strong></p>
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
    .print-header, .print-footer {
        display: block !important;
    }
    .table {
        font-size: 11px;
    }
    .card {
        border: none;
        box-shadow: none;
    }
    
    /* Force print badge colors */
    .badge {
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        color-adjust: exact !important;
    }
    
    /* Ensure badge backgrounds print */
    .badge.bg-info {
        background-color: #17a2b8 !important;
        color: white !important;
        border: 1px solid #17a2b8 !important;
    }
    
    .badge.bg-success {
        background-color: #28a745 !important;
        color: white !important;
        border: 1px solid #28a745 !important;
    }
    
    .badge.bg-danger {
        background-color: #dc3545 !important;
        color: white !important;
        border: 1px solid #dc3545 !important;
    }
    
    .badge.bg-warning {
        background-color: #ffc107 !important;
        color: #000 !important;
        border: 1px solid #ffc107 !important;
    }
    
    /* Ensure table header color prints */
    .table-danger {
        background-color: #f8d7da !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }
    
    /* Hide sort icons when printing */
    .sort-icon {
        display: none !important;
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
    // Get tanggal awal dan akhir dari form
    const tanggalAwal = document.querySelector('input[name="tanggal_awal"]').value;
    const tanggalAkhir = document.querySelector('input[name="tanggal_akhir"]').value;
    
    // Redirect ke export Excel dengan parameter tanggal
    window.location.href = `export_transaksi_keluar_excel.php?tanggal_awal=${tanggalAwal}&tanggal_akhir=${tanggalAkhir}`;
}
</script>

<!-- Include Table Utils -->
<script src="../assets/js/table-utils.js"></script>
<script>
    // Initialize table search and sort
    document.addEventListener('DOMContentLoaded', function() {
        initTable('tableLaporanKeluar', 'searchLaporanKeluar');
    });
</script>

<?php include '../includes/footer.php'; ?>