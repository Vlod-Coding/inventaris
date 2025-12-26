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
                <div class="card stat-card red">
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
                <div class="card stat-card orange">
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
            <div class="card-header d-flex justify-content-between align-items-center">
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
                <form method="GET" action="">
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
                    
                    <table class="table table-hover table-striped table-bordered">
                        <thead class="table-danger">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Tanggal</th>
                                <th width="12%">Kode Barang</th>
                                <th width="20%">Nama Barang</th>
                                <th width="12%">Kategori</th>
                                <th width="10%" class="text-center">Jumlah</th>
                                <th width="21%">Keterangan</th>
                                <th width="10%">Waktu Input</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (mysqli_num_rows($result) > 0):
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)): 
                            ?>
                                <tr>
                                    <td><?= $no++ ?></td>
                                    <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                    <td><strong><?= $row['kode_barang'] ?></strong></td>
                                    <td><?= $row['nama_barang'] ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= $row['kategori'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">
                                            -<?= number_format($row['jumlah']) ?> <?= $row['satuan'] ?>
                                        </span>
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
                                    <td>
                                        <small>
                                            <?= date('d/m/Y H:i', strtotime($row['created_at'])) ?>
                                        </small>
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
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">TOTAL:</th>
                                <th class="text-center">
                                    <strong><?= number_format($total_item_keluar) ?></strong>
                                </th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
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
    body * {
        visibility: hidden;
    }
    #printArea, #printArea * {
        visibility: visible;
    }
    #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
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
}
</style>

<!-- JavaScript -->
<script>
function printLaporan() {
    window.print();
}

function exportExcel() {
    Swal.fire({
        title: 'Export ke Excel',
        text: 'Fitur export akan segera tersedia',
        icon: 'info'
    });
}
</script>

<?php include '../includes/footer.php'; ?>