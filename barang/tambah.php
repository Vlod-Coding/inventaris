<?php
/**
 * ========================================
 * FORM TAMBAH BARANG
 * ========================================
 * File: barang/tambah.php
 * Fungsi: Form untuk menambah data barang baru
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';

// Set variabel untuk template
$page_title = 'Tambah Barang';
$page_icon = 'plus-circle';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '../index.php'],
    ['label' => 'Data Barang', 'url' => 'index.php'],
    ['label' => 'Tambah Barang']
];

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
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Card Form Tambah -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-plus-circle me-2"></i>
                        Form Tambah Barang Baru
                    </div>
                    <div class="card-body">
                        <form action="proses.php?aksi=tambah" method="POST" id="formTambah">
                            
                            <!-- Kode Barang -->
                            <div class="mb-3">
                                <label for="kode_barang" class="form-label">
                                    Kode Barang <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="kode_barang" 
                                       name="kode_barang" 
                                       placeholder="Contoh: BRG001"
                                       required
                                       autofocus>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Kode barang harus unik dan tidak boleh sama
                                </small>
                            </div>
                            
                            <!-- Nama Barang -->
                            <div class="mb-3">
                                <label for="nama_barang" class="form-label">
                                    Nama Barang <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nama_barang" 
                                       name="nama_barang" 
                                       placeholder="Contoh: Laptop Dell Inspiron 15"
                                       required>
                            </div>
                            
                            <!-- Kategori -->
                            <div class="mb-3">
                                <label for="kategori" class="form-label">
                                    Kategori <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="kategori" name="kategori" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="Elektronik">Elektronik</option>
                                    <option value="Alat Tulis">Alat Tulis</option>
                                    <option value="Furniture">Furniture</option>
                                    <option value="Makanan">Makanan</option>
                                    <option value="Minuman">Minuman</option>
                                    <option value="Pakaian">Pakaian</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            
                            <!-- Satuan -->
                            <div class="mb-3">
                                <label for="satuan" class="form-label">
                                    Satuan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="satuan" name="satuan" required>
                                    <option value="">-- Pilih Satuan --</option>
                                    <option value="Pcs">Pcs (Pieces)</option>
                                    <option value="Unit">Unit</option>
                                    <option value="Box">Box</option>
                                    <option value="Kg">Kg (Kilogram)</option>
                                    <option value="Gram">Gram</option>
                                    <option value="Liter">Liter</option>
                                    <option value="Meter">Meter</option>
                                    <option value="Set">Set</option>
                                    <option value="Lusin">Lusin</option>
                                </select>
                            </div>
                            
                            <!-- Stok Awal -->
                            <div class="mb-3">
                                <label for="stok" class="form-label">
                                    Stok Awal <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control" 
                                       id="stok" 
                                       name="stok" 
                                       placeholder="Masukkan jumlah stok awal"
                                       min="0"
                                       value="0"
                                       required>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Masukkan jumlah stok awal barang (bisa 0)
                                </small>
                            </div>
                            
                            <hr>
                            
                            <!-- Tombol Aksi -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Simpan Data
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-redo me-2"></i>Reset Form
                                </button>
                                <a href="index.php" class="btn btn-danger">
                                    <i class="fas fa-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Sidebar Info -->
            <div class="col-lg-4">
                <!-- Panduan Pengisian -->
                <div class="card border-info">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-lightbulb me-2"></i>
                        Panduan Pengisian
                    </div>
                    <div class="card-body">
                        <ol class="mb-0">
                            <li class="mb-2">
                                <strong>Kode Barang:</strong> Harus unik dan tidak boleh sama dengan kode lain
                            </li>
                            <li class="mb-2">
                                <strong>Nama Barang:</strong> Tulis nama lengkap dan jelas
                            </li>
                            <li class="mb-2">
                                <strong>Kategori:</strong> Pilih sesuai jenis barang
                            </li>
                            <li class="mb-2">
                                <strong>Satuan:</strong> Pilih satuan yang sesuai
                            </li>
                            <li class="mb-0">
                                <strong>Stok Awal:</strong> Boleh diisi 0 jika barang baru masuk nanti
                            </li>
                        </ol>
                    </div>
                </div>
                
                <!-- Tips -->
                <div class="card border-warning mt-3">
                    <div class="card-header bg-warning text-white">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Perhatian
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Pastikan kode barang belum pernah digunakan</li>
                            <li>Semua field bertanda <span class="text-danger">*</span> wajib diisi</li>
                            <li>Stok dapat diupdate melalui menu transaksi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<!-- Validasi Form dengan JavaScript -->
<script>
document.getElementById('formTambah').addEventListener('submit', function(e) {
    // Validasi kode barang (hanya alfanumerik)
    const kodeBarang = document.getElementById('kode_barang').value;
    const regex = /^[a-zA-Z0-9]+$/;
    
    if (!regex.test(kodeBarang)) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Kode Barang Tidak Valid',
            text: 'Kode barang hanya boleh berisi huruf dan angka tanpa spasi atau karakter khusus!',
        });
        return false;
    }
    
    // Validasi stok (tidak boleh negatif)
    const stok = parseInt(document.getElementById('stok').value);
    if (stok < 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Stok Tidak Valid',
            text: 'Stok tidak boleh bernilai negatif!',
        });
        return false;
    }
});
</script>

<?php include '../includes/footer.php'; ?>