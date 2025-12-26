<?php
/**
 * ========================================
 * FORM EDIT BARANG
 * ========================================
 * File: barang/edit.php
 * Fungsi: Form untuk mengupdate data barang
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';

// Set variabel untuk template
$page_title = 'Edit Barang';
$page_icon = 'edit';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '../index.php'],
    ['label' => 'Data Barang', 'url' => 'index.php'],
    ['label' => 'Edit Barang']
];

// Cek apakah ada parameter id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?status=error&msg=ID tidak ditemukan');
    exit;
}

$id = escape($_GET['id']);

// Query untuk mengambil data barang berdasarkan ID
$query = "SELECT * FROM barang WHERE id = '$id' LIMIT 1";
$result = mysqli_query($conn, $query);

// Cek apakah data ditemukan
if (mysqli_num_rows($result) == 0) {
    header('Location: index.php?status=error&msg=Data barang tidak ditemukan');
    exit;
}

$data = mysqli_fetch_assoc($result);

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
                <!-- Card Form Edit -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-edit me-2"></i>
                        Form Edit Data Barang
                    </div>
                    <div class="card-body">
                        <form action="proses.php?aksi=edit" method="POST" id="formEdit">
                            
                            <!-- Hidden ID -->
                            <input type="hidden" name="id" value="<?= $data['id'] ?>">
                            
                            <!-- Kode Barang -->
                            <div class="mb-3">
                                <label for="kode_barang" class="form-label">
                                    Kode Barang <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="kode_barang" 
                                       name="kode_barang" 
                                       value="<?= $data['kode_barang'] ?>"
                                       required
                                       autofocus>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Kode barang harus unik
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
                                       value="<?= $data['nama_barang'] ?>"
                                       required>
                            </div>
                            
                            <!-- Kategori -->
                            <div class="mb-3">
                                <label for="kategori" class="form-label">
                                    Kategori <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="kategori" name="kategori" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    <option value="Elektronik" <?= ($data['kategori'] == 'Elektronik') ? 'selected' : '' ?>>Elektronik</option>
                                    <option value="Alat Tulis" <?= ($data['kategori'] == 'Alat Tulis') ? 'selected' : '' ?>>Alat Tulis</option>
                                    <option value="Furniture" <?= ($data['kategori'] == 'Furniture') ? 'selected' : '' ?>>Furniture</option>
                                    <option value="Makanan" <?= ($data['kategori'] == 'Makanan') ? 'selected' : '' ?>>Makanan</option>
                                    <option value="Minuman" <?= ($data['kategori'] == 'Minuman') ? 'selected' : '' ?>>Minuman</option>
                                    <option value="Pakaian" <?= ($data['kategori'] == 'Pakaian') ? 'selected' : '' ?>>Pakaian</option>
                                    <option value="Lainnya" <?= ($data['kategori'] == 'Lainnya') ? 'selected' : '' ?>>Lainnya</option>
                                </select>
                            </div>
                            
                            <!-- Satuan -->
                            <div class="mb-3">
                                <label for="satuan" class="form-label">
                                    Satuan <span class="text-danger">*</span>
                                </label>
                                <select class="form-select" id="satuan" name="satuan" required>
                                    <option value="">-- Pilih Satuan --</option>
                                    <option value="Pcs" <?= ($data['satuan'] == 'Pcs') ? 'selected' : '' ?>>Pcs (Pieces)</option>
                                    <option value="Unit" <?= ($data['satuan'] == 'Unit') ? 'selected' : '' ?>>Unit</option>
                                    <option value="Box" <?= ($data['satuan'] == 'Box') ? 'selected' : '' ?>>Box</option>
                                    <option value="Kg" <?= ($data['satuan'] == 'Kg') ? 'selected' : '' ?>>Kg (Kilogram)</option>
                                    <option value="Gram" <?= ($data['satuan'] == 'Gram') ? 'selected' : '' ?>>Gram</option>
                                    <option value="Liter" <?= ($data['satuan'] == 'Liter') ? 'selected' : '' ?>>Liter</option>
                                    <option value="Meter" <?= ($data['satuan'] == 'Meter') ? 'selected' : '' ?>>Meter</option>
                                    <option value="Set" <?= ($data['satuan'] == 'Set') ? 'selected' : '' ?>>Set</option>
                                    <option value="Lusin" <?= ($data['satuan'] == 'Lusin') ? 'selected' : '' ?>>Lusin</option>
                                </select>
                            </div>
                            
                            <!-- Stok Saat Ini (Read Only) -->
                            <div class="mb-3">
                                <label for="stok_display" class="form-label">
                                    Stok Saat Ini
                                </label>
                                <input type="text" 
                                       class="form-control" 
                                       id="stok_display" 
                                       value="<?= number_format($data['stok']) ?> <?= $data['satuan'] ?>"
                                       readonly
                                       disabled>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Stok tidak dapat diubah di sini. Gunakan menu Transaksi untuk mengubah stok.
                                </small>
                            </div>
                            
                            <!-- Info Terakhir Update -->
                            <div class="alert alert-info">
                                <i class="fas fa-clock me-2"></i>
                                <strong>Terakhir diupdate:</strong> 
                                <?= tgl_indo(date('Y-m-d', strtotime($data['updated_at']))) ?> 
                                pukul <?= date('H:i', strtotime($data['updated_at'])) ?> WIB
                            </div>
                            
                            <hr>
                            
                            <!-- Tombol Aksi -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Data
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
                <!-- Info Data Lama -->
                <div class="card border-secondary">
                    <div class="card-header bg-secondary text-white">
                        <i class="fas fa-database me-2"></i>
                        Data Sebelumnya
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th>Kode</th>
                                <td>: <?= $data['kode_barang'] ?></td>
                            </tr>
                            <tr>
                                <th>Nama</th>
                                <td>: <?= $data['nama_barang'] ?></td>
                            </tr>
                            <tr>
                                <th>Kategori</th>
                                <td>: <?= $data['kategori'] ?></td>
                            </tr>
                            <tr>
                                <th>Satuan</th>
                                <td>: <?= $data['satuan'] ?></td>
                            </tr>
                            <tr>
                                <th>Stok</th>
                                <td>: <?= number_format($data['stok']) ?> <?= $data['satuan'] ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <!-- Peringatan -->
                <div class="card border-warning mt-3">
                    <div class="card-header bg-warning text-white">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Perhatian
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>Pastikan kode barang tidak duplikat dengan data lain</li>
                            <li>Perubahan data akan langsung tersimpan</li>
                            <li>Stok hanya bisa diubah melalui menu Transaksi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

<!-- Validasi Form dengan JavaScript -->
<script>
document.getElementById('formEdit').addEventListener('submit', function(e) {
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
});
</script>

<?php include '../includes/footer.php'; ?>