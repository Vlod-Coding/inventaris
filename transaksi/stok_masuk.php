<?php
/**
 * ========================================
 * TRANSAKSI STOK MASUK
 * ========================================
 * File: transaksi/stok_masuk.php
 * Fungsi: Form input stok masuk dan history transaksi
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';

// Set variabel untuk template
$page_title = 'Stok Masuk';
$page_icon = 'arrow-down';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '../index.php'],
    ['label' => 'Stok Masuk']
];

// Query untuk mengambil daftar barang
$query_barang = "SELECT id, kode_barang, nama_barang, satuan, stok 
                 FROM barang 
                 ORDER BY nama_barang ASC";
$result_barang = mysqli_query($conn, $query_barang);

// Query untuk history stok masuk (10 terakhir)
$query_history = "SELECT sm.*, b.kode_barang, b.nama_barang, b.satuan
                  FROM stok_masuk sm
                  JOIN barang b ON sm.barang_id = b.id
                  ORDER BY sm.created_at DESC
                  LIMIT 10";
$result_history = mysqli_query($conn, $query_history);

// Cek notifikasi
$notif = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $notif = alert('success', '<i class="fas fa-check-circle me-2"></i>Transaksi stok masuk berhasil disimpan!');
    } elseif ($_GET['status'] == 'error') {
        $notif = alert('danger', '<i class="fas fa-times-circle me-2"></i>Terjadi kesalahan: ' . ($_GET['msg'] ?? ''));
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
        
        <div class="row">
            <!-- Form Stok Masuk -->
            <div class="col-lg-5 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-arrow-down me-2"></i>
                        Form Transaksi Stok Masuk
                    </div>
                    <div class="card-body">
                        <form action="proses_masuk.php" method="POST" id="formStokMasuk">
                            
                            <!-- Tanggal -->
                            <div class="mb-3">
                                <label for="tanggal" class="form-label">
                                    Tanggal <span class="text-danger">*</span>
                                </label>
                                <input type="date" 
                                       class="form-control" 
                                       id="tanggal" 
                                       name="tanggal" 
                                       value="<?= date('Y-m-d') ?>"
                                       max="<?= date('Y-m-d') ?>"
                                       required>
                            </div>
                            
                            <!-- Pilih Barang -->
                            <div class="mb-3">
                                <label for="barang_id" class="form-label">
                                    Pilih Barang <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" 
                                        id="barang_id" 
                                        name="barang_id" 
                                        required
                                        onchange="updateInfoBarang()">
                                    <option value="">-- Pilih Barang --</option>
                                    <?php while ($row = mysqli_fetch_assoc($result_barang)): ?>
                                        <option value="<?= $row['id'] ?>" 
                                                data-kode="<?= $row['kode_barang'] ?>"
                                                data-nama="<?= $row['nama_barang'] ?>"
                                                data-satuan="<?= $row['satuan'] ?>"
                                                data-stok="<?= $row['stok'] ?>">
                                            [<?= $row['kode_barang'] ?>] <?= $row['nama_barang'] ?> 
                                            (Stok: <?= number_format($row['stok']) ?> <?= $row['satuan'] ?>)
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            
                            <!-- Info Barang Terpilih -->
                            <div id="infoBarang" class="alert alert-info d-none mb-3">
                                <strong>Info Barang:</strong><br>
                                <span id="displayKode"></span><br>
                                <span id="displayNama"></span><br>
                                <span id="displayStok"></span>
                            </div>
                            
                            <!-- Jumlah Masuk -->
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">
                                    Jumlah Masuk <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="jumlah" 
                                       name="jumlah" 
                                       placeholder="Masukkan jumlah stok yang masuk"
                                       min="1"
                                       required>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Stok akan otomatis bertambah
                                </small>
                            </div>
                            
                            <!-- Supplier -->
                            <div class="mb-3">
                                <label for="supplier" class="form-label">
                                    Supplier/Pemasok
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="supplier" 
                                       name="supplier" 
                                       placeholder="Nama supplier (opsional)">
                            </div>
                            
                            <hr>
                            
                            <!-- Tombol Aksi -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>Simpan Transaksi
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-redo me-2"></i>Reset
                                </button>
                            </div>
                            
                        </form>
                    </div>
                </div>
                
                <!-- Info Box -->
                <div class="card border-warning mt-3">
                    <div class="card-header bg-warning text-white">
                        <i class="fas fa-lightbulb me-2"></i>
                        Informasi
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Stok akan otomatis bertambah setelah transaksi disimpan</li>
                            <li>Tanggal transaksi tidak boleh melebihi hari ini</li>
                            <li>Supplier bersifat opsional</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- History Stok Masuk -->
            <div class="col-lg-7 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <i class="fas fa-history me-2"></i>
                        History Transaksi Stok Masuk (10 Terakhir)
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="12%">Tanggal</th>
                                        <th width="15%">Kode</th>
                                        <th width="28%">Nama Barang</th>
                                        <th width="15%" class="text-center">Jumlah</th>
                                        <th width="25%">Supplier</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    if (mysqli_num_rows($result_history) > 0):
                                        $no = 1;
                                        while ($row = mysqli_fetch_assoc($result_history)): 
                                    ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                                            <td><strong><?= $row['kode_barang'] ?></strong></td>
                                            <td><?= $row['nama_barang'] ?></td>
                                            <td class="text-center">
                                                <span class="badge bg-success">
                                                    +<?= number_format($row['jumlah']) ?> <?= $row['satuan'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= $row['supplier'] ? $row['supplier'] : '<span class="text-muted">-</span>' ?>
                                            </td>
                                        </tr>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">
                                                <i class="fas fa-inbox fa-3x mb-2"></i><br>
                                                Belum ada transaksi stok masuk
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Link ke Laporan Lengkap -->
                        <div class="text-end mt-3">
                            <a href="../laporan/transaksi_masuk.php" class="btn btn-sm btn-outline-success">
                                <i class="fas fa-file-alt me-2"></i>Lihat Laporan Lengkap
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<!-- JavaScript untuk Update Info Barang -->
<script>
function updateInfoBarang() {
    const select = document.getElementById('barang_id');
    const option = select.options[select.selectedIndex];
    const infoBox = document.getElementById('infoBarang');
    
    if (option.value) {
        // Ambil data dari atribut option
        const kode = option.getAttribute('data-kode');
        const nama = option.getAttribute('data-nama');
        const satuan = option.getAttribute('data-satuan');
        const stok = option.getAttribute('data-stok');
        
        // Update info
        document.getElementById('displayKode').textContent = 'Kode: ' + kode;
        document.getElementById('displayNama').textContent = 'Nama: ' + nama;
        document.getElementById('displayStok').textContent = 'Stok Saat Ini: ' + 
            Number(stok).toLocaleString() + ' ' + satuan;
        
        // Tampilkan info box
        infoBox.classList.remove('d-none');
    } else {
        // Sembunyikan info box
        infoBox.classList.add('d-none');
    }
}

// Validasi form
document.getElementById('formStokMasuk').addEventListener('submit', function(e) {
    const jumlah = parseInt(document.getElementById('jumlah').value);
    
    if (jumlah <= 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Jumlah Tidak Valid',
            text: 'Jumlah stok masuk harus lebih dari 0!',
        });
        return false;
    }
});
</script>

<?php include '../includes/footer.php'; ?>