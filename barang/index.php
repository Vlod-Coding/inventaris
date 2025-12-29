<?php
/**
 * ========================================
 * LIST DATA BARANG
 * ========================================
 * File: barang/index.php
 * Fungsi: Menampilkan daftar semua barang dengan DataTables
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';
require_once '../config/permissions.php';

// Check if user can access this page (CS and Admin only)
check_page_access('barang');

// Set variabel untuk template
$page_title = 'Data Barang';
$page_icon = 'box';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '../index.php'],
    ['label' => 'Data Barang']
];

// Query untuk mengambil semua data barang
$query = "SELECT * FROM barang ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// Cek apakah ada pesan notifikasi
$notif = '';
$show_cascade_popup = false;
$cascade_data = null;

if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success_add') {
        $notif = alert('success', '<i class="fas fa-check-circle me-2"></i>Data barang berhasil ditambahkan!');
    } elseif ($_GET['status'] == 'success_edit') {
        $notif = alert('success', '<i class="fas fa-check-circle me-2"></i>Data barang berhasil diupdate!');
    } elseif ($_GET['status'] == 'success_delete') {
        $notif = alert('success', '<i class="fas fa-check-circle me-2"></i>Data barang berhasil dihapus!');
    } elseif ($_GET['status'] == 'error') {
        $notif = alert('danger', '<i class="fas fa-times-circle me-2"></i>Terjadi kesalahan! ' . ($_GET['msg'] ?? ''));
    } elseif ($_GET['status'] == 'has_transaction' && isset($_GET['data'])) {
        $show_cascade_popup = true;
        $cascade_data = json_decode(urldecode($_GET['data']), true);
    }
}

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
        
        <!-- Notifikasi -->
        <?= $notif ?>
        
        <!-- Card Data Barang -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-list me-2"></i>
                    Daftar Barang
                </span>
                <?php if (can_manage_barang()): ?>
                <a href="tambah.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-2"></i>Tambah Barang
                </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <!-- Search Box -->
                <div class="mb-3 d-flex justify-content-end">
                    <div class="input-group" style="width: 350px;">
                        <span class="input-group-text bg-light">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" 
                               id="searchBarang" 
                               class="form-control" 
                               placeholder="Cari data..."
                               autocomplete="off">
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table id="tableBarang" class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center sortable">No</th>
                                <th width="15%" class="text-center sortable">Kode Barang</th>
                                <th width="25%" class="text-center sortable">Nama Barang</th>
                                <th width="15%" class="text-center sortable">Kategori</th>
                                <th width="10%" class="text-center sortable">Satuan</th>
                                <th width="10%" class="text-center sortable">Stok</th>
                                <th width="20%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            $barang_data = []; // Store data for modals
                            mysqli_data_seek($result, 0); // Reset pointer
                            while ($row = mysqli_fetch_assoc($result)): 
                                $barang_data[] = $row; // Save for modals later
                            ?>
                                <tr class="text-center">
                                    <td><?= $no++ ?></td>
                                    <td>
                                        <strong><?= $row['kode_barang'] ?></strong>
                                    </td>
                                    <td><?= $row['nama_barang'] ?></td>
                                    <td>
                                        <span class="badge bg-info">
                                            <?= $row['kategori'] ?>
                                        </span>
                                    </td>
                                    <td><?= $row['satuan'] ?></td>
                                    <td class="text-center" data-sort="<?= $row['stok'] ?>">
                                        <?php
                                        // Tentukan warna badge berdasarkan jumlah stok
                                        if ($row['stok'] < 10) {
                                            $badge_class = 'bg-danger'; // Merah untuk stok < 10
                                        } elseif ($row['stok'] < 50) {
                                            $badge_class = 'bg-warning'; // Kuning untuk stok 10-49
                                        } else {
                                            $badge_class = 'bg-success'; // Hijau untuk stok >= 50
                                        }
                                        ?>
                                        <span class="badge <?= $badge_class ?>">
                                            <?= number_format($row['stok']) ?> <?= $row['satuan'] ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <!-- Tombol Detail -->
                                        <button type="button" 
                                                class="btn btn-info btn-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalDetail<?= $row['id'] ?>"
                                                title="Detail">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <?php if (can_manage_barang()): ?>
                                        <!-- Tombol Edit -->
                                        <a href="edit.php?id=<?= $row['id'] ?>" 
                                           class="btn btn-warning btn-sm"
                                           title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <!-- Tombol Hapus -->
                                        <a href="javascript:void(0)" 
                                           onclick="confirmDelete('hapus.php?id=<?= $row['id'] ?>', '<?= $row['nama_barang'] ?>')" 
                                           class="btn btn-danger btn-sm"
                                           title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Modals Section - Outside Table Structure -->
        <?php foreach ($barang_data as $item): ?>
        <div class="modal fade" id="modalDetail<?= $item['id'] ?>" tabindex="-1" aria-labelledby="modalDetailLabel<?= $item['id'] ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="modalDetailLabel<?= $item['id'] ?>">
                            <i class="fas fa-info-circle me-2"></i>
                            Detail Barang
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-borderless mb-0">
                            <tr>
                                <th width="40%">Kode Barang</th>
                                <td><strong><?= $item['kode_barang'] ?></strong></td>
                            </tr>
                            <tr>
                                <th>Nama Barang</th>
                                <td><?= $item['nama_barang'] ?></td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td><span class="badge bg-info"><?= $item['kategori'] ?></span></td>
                            </tr>
                            <tr>
                                <th>Satuan</th>
                                <td><?= $item['satuan'] ?></td>
                            </tr>
                            <tr>
                                <th>Stok</th>
                                <td>
                                    <?php
                                    if ($item['stok'] < 10) {
                                        $badge_class = 'bg-danger';
                                    } elseif ($item['stok'] < 50) {
                                        $badge_class = 'bg-warning';
                                    } else {
                                        $badge_class = 'bg-success';
                                    }
                                    ?>
                                    <span class="badge <?= $badge_class ?>">
                                        <?= number_format($item['stok']) ?> <?= $item['satuan'] ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        
    </div>
</div>

<!-- Include Table Utils -->
<script src="../assets/js/table-utils.js"></script>
<script>
    // Initialize table search and sort
    document.addEventListener('DOMContentLoaded', function() {
        initTable('tableBarang', 'searchBarang');
        
        <?php if ($show_cascade_popup && $cascade_data): ?>
        // Show cascade delete confirmation popup
        showCascadeDeletePopup(<?= json_encode($cascade_data) ?>);
        <?php endif; ?>
    });
    
    function showCascadeDeletePopup(data) {
        Swal.fire({
            title: '<i class="fas fa-exclamation-triangle text-warning"></i> Peringatan!',
            html: `
                <div class="text-start">
                    <p class="mb-3"><strong>Barang yang akan dihapus:</strong></p>
                    <ul class="list-unstyled mb-3">
                        <li><i class="fas fa-barcode me-2"></i><strong>Kode:</strong> ${data.kode}</li>
                        <li><i class="fas fa-box me-2"></i><strong>Nama:</strong> ${data.nama}</li>
                    </ul>
                    
                    <div class="alert alert-danger mb-3">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Barang ini memiliki ${data.total_transaksi} riwayat transaksi:</strong>
                        <ul class="mt-2 mb-0">
                            <li>${data.total_masuk} transaksi stok masuk</li>
                            <li>${data.total_keluar} transaksi stok keluar</li>
                        </ul>
                    </div>
                    
                    <p class="text-danger mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Jika Anda melanjutkan, SEMUA riwayat transaksi akan ikut terhapus permanen!</strong>
                    </p>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmCascadeDelete">
                        <label class="form-check-label" for="confirmCascadeDelete">
                            <strong>Saya mengerti dan yakin ingin menghapus barang beserta semua riwayat transaksinya</strong>
                        </label>
                    </div>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-trash me-2"></i>Ya, Hapus Semua!',
            cancelButtonText: '<i class="fas fa-times me-2"></i>Batal',
            width: '600px',
            preConfirm: () => {
                const checkbox = document.getElementById('confirmCascadeDelete');
                if (!checkbox.checked) {
                    Swal.showValidationMessage('Anda harus mencentang checkbox konfirmasi!');
                    return false;
                }
                return true;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke hapus.php dengan parameter force=1
                window.location.href = 'hapus.php?id=' + data.id + '&force=1';
            }
        });
    }
</script>

<?php include '../includes/footer.php'; ?>