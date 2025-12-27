<?php
/**
 * ========================================
 * TRANSAKSI STOK KELUAR
 * ========================================
 * File: transaksi/stok_keluar.php
 * Fungsi: Form input stok keluar dan history transaksi
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';

// Set variabel untuk template
$page_title = 'Stok Keluar';
$page_icon = 'arrow-up';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '../index.php'],
    ['label' => 'Stok Keluar']
];

// Query untuk mengambil daftar barang yang masih ada stoknya
$query_barang = "SELECT id, kode_barang, nama_barang, satuan, stok 
                 FROM barang 
                 WHERE stok > 0
                 ORDER BY nama_barang ASC";
$result_barang = mysqli_query($conn, $query_barang);

// Query untuk history stok keluar (10 terakhir)
$query_history = "SELECT sk.*, b.kode_barang, b.nama_barang, b.satuan
                  FROM stok_keluar sk
                  JOIN barang b ON sk.barang_id = b.id
                  ORDER BY sk.created_at DESC
                  LIMIT 10";
$result_history = mysqli_query($conn, $query_history);

// Cek notifikasi
$notif = '';
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        $notif = alert('success', '<i class="fas fa-check-circle me-2"></i>Transaksi stok keluar berhasil disimpan!');
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
            <!-- Form Stok Keluar -->
            <div class="col-lg-5 mb-4">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <i class="fas fa-arrow-up me-2"></i>
                        Form Transaksi Stok Keluar
                    </div>
                    <div class="card-body">
                        <form action="proses_keluar.php" method="POST" id="formStokKeluar">
                            
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
                                    <?php 
                                    if (mysqli_num_rows($result_barang) > 0):
                                        while ($row = mysqli_fetch_assoc($result_barang)): 
                                    ?>
                                        <option value="<?= $row['id'] ?>" 
                                                data-kode="<?= $row['kode_barang'] ?>"
                                                data-nama="<?= $row['nama_barang'] ?>"
                                                data-satuan="<?= $row['satuan'] ?>"
                                                data-stok="<?= $row['stok'] ?>">
                                            [<?= $row['kode_barang'] ?>] <?= $row['nama_barang'] ?> 
                                            (Stok: <?= number_format($row['stok']) ?> <?= $row['satuan'] ?>)
                                        </option>
                                    <?php 
                                        endwhile;
                                    endif;
                                    ?>
                                </select>
                                <?php if (mysqli_num_rows($result_barang) == 0): ?>
                                    <small class="text-danger">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Tidak ada barang dengan stok tersedia
                                    </small>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Info Barang Terpilih -->
                            <div id="infoBarang" class="alert alert-info d-none mb-3">
                                <strong>Info Barang:</strong><br>
                                <span id="displayKode"></span><br>
                                <span id="displayNama"></span><br>
                                <span id="displayStok"></span>
                                <input type="hidden" id="stok_tersedia" value="0">
                            </div>
                            
                            <!-- Jumlah Keluar -->
                            <div class="mb-3">
                                <label for="jumlah" class="form-label">
                                    Jumlah Keluar <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="jumlah" 
                                       name="jumlah" 
                                       placeholder="Masukkan jumlah stok yang keluar"
                                       min="1"
                                       required
                                       onkeyup="validasiStok()">
                                <small id="warningStok" class="text-danger d-none">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Jumlah melebihi stok tersedia!
                                </small>
                                <small class="text-muted d-block mt-1">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Stok akan otomatis berkurang
                                </small>
                            </div>
                            
                            <!-- Keterangan -->
                            <div class="mb-3">
                                <label for="keterangan" class="form-label">
                                    Keterangan
                                </label>
                                <textarea class="form-control" 
                                          id="keterangan" 
                                          name="keterangan" 
                                          rows="2"
                                          placeholder="Keterangan penggunaan barang (opsional)"></textarea>
                            </div>
                            
                            <!-- Penanggung Jawab -->
                            <div class="mb-3">
                                <label for="penanggung_jawab" class="form-label">
                                    Penanggung Jawab <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="penanggung_jawab" 
                                       name="penanggung_jawab" 
                                       placeholder="Nama penanggung jawab / yang mengambil barang"
                                       required>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Wajib diisi untuk tracking barang yang diambil
                                </small>
                            </div>
                            
                            <hr>
                            
                            <!-- Tombol Aksi -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-danger" id="btnSubmit">
                                    <i class="fas fa-save me-2"></i>Simpan Transaksi
                                </button>
                                <button type="reset" class="btn btn-secondary" onclick="resetForm()">
                                    <i class="fas fa-redo me-2"></i>Reset
                                </button>
                            </div>
                            
                        </form>
                    </div>
                </div>
                
                <!-- Info Box -->
                <div class="card border-warning mt-3">
                    <div class="card-header bg-warning text-white">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Perhatian
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Stok akan otomatis berkurang setelah transaksi disimpan</li>
                            <li>Jumlah keluar tidak boleh melebihi stok tersedia</li>
                            <li>Pastikan memilih barang yang tepat</li>
                            <li>Transaksi tidak dapat dibatalkan</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- History Stok Keluar -->
            <div class="col-lg-7 mb-4">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <i class="fas fa-history me-2"></i>
                        History Transaksi Stok Keluar (10 Terakhir)
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th width="5%">No</th>
                                        <th width="10%">Tanggal</th>
                                        <th width="12%">Kode</th>
                                        <th width="20%">Nama Barang</th>
                                        <th width="10%" class="text-center">Jumlah</th>
                                        <th width="18%">Penanggung Jawab</th>
                                        <th width="25%">Keterangan</th>
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
                                                <span class="badge bg-danger">
                                                    -<?= number_format($row['jumlah']) ?> <?= $row['satuan'] ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?= !empty($row['penanggung_jawab']) ? $row['penanggung_jawab'] : '<span class="text-muted">-</span>' ?>
                                            </td>
                                            <td>
                                                <?= $row['keterangan'] ? $row['keterangan'] : '<span class="text-muted">-</span>' ?>
                                            </td>
                                        </tr>
                                    <?php 
                                        endwhile;
                                    else:
                                    ?>
                                        <tr>
                                            <td colspan="7" class="text-center text-muted">
                                                <i class="fas fa-inbox fa-3x mb-2"></i><br>
                                                Belum ada transaksi stok keluar
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Link ke Laporan Lengkap -->
                        <div class="text-end mt-3">
                            <a href="../laporan/transaksi_keluar.php" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-file-alt me-2"></i>Lihat Laporan Lengkap
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<!-- JavaScript -->
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
        document.getElementById('displayStok').textContent = 'Stok Tersedia: ' + 
            Number(stok).toLocaleString() + ' ' + satuan;
        
        // Simpan stok tersedia
        document.getElementById('stok_tersedia').value = stok;
        
        // Tampilkan info box
        infoBox.classList.remove('d-none');
        
        // Reset jumlah input
        document.getElementById('jumlah').value = '';
        document.getElementById('warningStok').classList.add('d-none');
    } else {
        // Sembunyikan info box
        infoBox.classList.add('d-none');
        document.getElementById('stok_tersedia').value = 0;
    }
}

function validasiStok() {
    const stokTersedia = parseInt(document.getElementById('stok_tersedia').value);
    const jumlahKeluar = parseInt(document.getElementById('jumlah').value) || 0;
    const warningStok = document.getElementById('warningStok');
    const btnSubmit = document.getElementById('btnSubmit');
    
    if (jumlahKeluar > stokTersedia) {
        // Jumlah melebihi stok
        warningStok.classList.remove('d-none');
        btnSubmit.disabled = true;
    } else {
        // Jumlah valid
        warningStok.classList.add('d-none');
        btnSubmit.disabled = false;
    }
}

function resetForm() {
    document.getElementById('infoBarang').classList.add('d-none');
    document.getElementById('warningStok').classList.add('d-none');
    document.getElementById('btnSubmit').disabled = false;
}

// Validasi form saat submit
document.getElementById('formStokKeluar').addEventListener('submit', function(e) {
    const stokTersedia = parseInt(document.getElementById('stok_tersedia').value);
    const jumlahKeluar = parseInt(document.getElementById('jumlah').value);
    
    if (jumlahKeluar <= 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Jumlah Tidak Valid',
            text: 'Jumlah stok keluar harus lebih dari 0!',
        });
        return false;
    }
    
    if (jumlahKeluar > stokTersedia) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Stok Tidak Mencukupi',
            text: 'Jumlah keluar melebihi stok yang tersedia!',
        });
        return false;
    }
});
</script>

<?php include '../includes/footer.php'; ?>