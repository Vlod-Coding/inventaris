<?php
/**
 * ========================================
 * TRANSAKSI STOK KELUAR - MULTIPLE ITEMS
 * ========================================
 * File: transaksi/stok_keluar_multi.php
 * Fungsi: Form input stok keluar dengan multiple items
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';
require_once '../config/permissions.php';

// Check if user can access this page
check_page_access('transaksi');

// Set variabel untuk template
$page_title = 'Stok Keluar (Multi Item)';
$page_icon = 'arrow-up';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '../index.php'],
    ['label' => 'Stok Keluar (Multi Item)']
];

// Query untuk mengambil daftar barang yang masih ada stoknya
$query_barang = "SELECT id, kode_barang, nama_barang, satuan, stok 
                 FROM barang 
                 WHERE stok > 0
                 ORDER BY nama_barang ASC";
$result_barang = mysqli_query($conn, $query_barang);

// Convert to array for JavaScript
$barang_list = [];
while ($row = mysqli_fetch_assoc($result_barang)) {
    $barang_list[] = $row;
}

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
        
        <!-- Form Stok Keluar Multi Item -->
        <div class="card">
            <div class="card-header bg-danger text-white">
                <i class="fas fa-arrow-up me-2"></i>
                Form Transaksi Stok Keluar (Multiple Items)
            </div>
            <div class="card-body">
                <form action="proses_keluar_multi.php" method="POST" id="formStokKeluar">
                    
                    <!-- Common Fields -->
                    <div class="row mb-4">
                        <div class="col-md-6">
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
                        
                        <div class="col-md-6">
                            <label for="penanggung_jawab" class="form-label">
                                Penanggung Jawab <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control" 
                                   id="penanggung_jawab" 
                                   name="penanggung_jawab" 
                                   placeholder="Nama penanggung jawab / yang mengambil barang"
                                   required>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <!-- Dynamic Item Rows -->
                    <div id="itemsContainer">
                        <!-- Item rows will be added here -->
                    </div>
                    
                    <!-- Add Item Button -->
                    <div class="mb-4">
                        <button type="button" class="btn btn-outline-success" onclick="addItemRow()">
                            <i class="fas fa-plus me-2"></i>Tambah Barang
                        </button>
                    </div>
                    
                    <hr>
                    
                    <!-- Submit Buttons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-danger" id="btnSubmit">
                            <i class="fas fa-save me-2"></i>Simpan Transaksi
                        </button>
                        <button type="reset" class="btn btn-secondary" onclick="resetForm()">
                            <i class="fas fa-redo me-2"></i>Reset
                        </button>
                        <a href="stok_keluar.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Form Single Item
                        </a>
                    </div>
                    
                </form>
            </div>
        </div>
        
        <!-- Info Box -->
        <div class="card mt-3">
            <div class="card-header bg-warning text-white">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Perhatian
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Anda dapat menambahkan multiple barang dalam satu transaksi</li>
                    <li>Stok akan otomatis berkurang setelah transaksi disimpan</li>
                    <li>Jumlah keluar tidak boleh melebihi stok tersedia</li>
                    <li>Tidak boleh ada barang yang sama dalam satu transaksi</li>
                    <li>Transaksi tidak dapat dibatalkan</li>
                </ul>
            </div>
        </div>
        
    </div>
</div>

<!-- Item Row Template (Hidden) -->
<template id="itemRowTemplate">
    <div class="item-row card mb-3">
        <div class="card-body">
            <div class="row align-items-start">
                <div class="col-md-4">
                    <label class="form-label">Pilih Barang <span class="text-danger">*</span></label>
                    <select class="form-select barang-select" name="items[{INDEX}][barang_id]" required onchange="updateStokInfo(this, {INDEX})">
                        <option value="">-- Pilih Barang --</option>
                        <?php foreach ($barang_list as $barang): ?>
                            <option value="<?= $barang['id'] ?>" 
                                    data-kode="<?= $barang['kode_barang'] ?>"
                                    data-nama="<?= $barang['nama_barang'] ?>"
                                    data-satuan="<?= $barang['satuan'] ?>"
                                    data-stok="<?= $barang['stok'] ?>">
                                [<?= $barang['kode_barang'] ?>] <?= $barang['nama_barang'] ?> 
                                (Stok: <?= number_format($barang['stok']) ?> <?= $barang['satuan'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="stok-info text-muted d-block" style="min-height: 20px;">&nbsp;</small>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Jumlah <span class="text-danger">*</span></label>
                    <input type="number" 
                           class="form-control jumlah-input" 
                           name="items[{INDEX}][jumlah]" 
                           placeholder="Jumlah"
                           min="1"
                           required
                           onkeyup="validateStok(this, {INDEX})">
                    <small class="warning-stok text-danger d-none" style="min-height: 20px;">
                        <i class="fas fa-exclamation-triangle"></i> Melebihi stok!
                    </small>
                    <small class="d-block" style="min-height: 20px;">&nbsp;</small>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Keterangan</label>
                    <input type="text" 
                           class="form-control" 
                           name="items[{INDEX}][keterangan]" 
                           placeholder="Keterangan (opsional)">
                    <small class="d-block" style="min-height: 20px;">&nbsp;</small>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeItemRow(this)">
                        <i class="fas fa-trash"></i> Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- JavaScript -->
<script>
// Barang data for validation
const barangData = <?= json_encode($barang_list) ?>;
let itemIndex = 0;

// Add first row on page load
document.addEventListener('DOMContentLoaded', function() {
    addItemRow();
});

function addItemRow() {
    const template = document.getElementById('itemRowTemplate');
    const container = document.getElementById('itemsContainer');
    
    // Clone template
    const clone = template.content.cloneNode(true);
    
    // Replace {INDEX} with actual index
    const html = clone.querySelector('.item-row').outerHTML.replace(/{INDEX}/g, itemIndex);
    
    // Add to container
    container.insertAdjacentHTML('beforeend', html);
    
    itemIndex++;
}

function removeItemRow(button) {
    const itemRow = button.closest('.item-row');
    const container = document.getElementById('itemsContainer');
    
    // Don't allow removing if only one item left
    if (container.querySelectorAll('.item-row').length <= 1) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Bisa Dihapus',
            text: 'Minimal harus ada 1 barang dalam transaksi!'
        });
        return;
    }
    
    itemRow.remove();
}

function updateStokInfo(select, index) {
    const option = select.options[select.selectedIndex];
    const stokInfo = select.closest('.item-row').querySelector('.stok-info');
    
    if (option.value) {
        const stok = option.getAttribute('data-stok');
        const satuan = option.getAttribute('data-satuan');
        stokInfo.textContent = `Stok tersedia: ${Number(stok).toLocaleString()} ${satuan}`;
        stokInfo.setAttribute('data-stok', stok);
        
        // Check for duplicates
        checkDuplicateBarang();
    } else {
        stokInfo.textContent = '';
        stokInfo.setAttribute('data-stok', '0');
    }
}

function validateStok(input, index) {
    const itemRow = input.closest('.item-row');
    const stokInfo = itemRow.querySelector('.stok-info');
    const warningStok = itemRow.querySelector('.warning-stok');
    const stokTersedia = parseInt(stokInfo.getAttribute('data-stok')) || 0;
    const jumlah = parseInt(input.value) || 0;
    
    if (jumlah > stokTersedia) {
        warningStok.classList.remove('d-none');
        input.classList.add('is-invalid');
    } else {
        warningStok.classList.add('d-none');
        input.classList.remove('is-invalid');
    }
}

function checkDuplicateBarang() {
    const selects = document.querySelectorAll('.barang-select');
    const selectedIds = [];
    let hasDuplicate = false;
    
    selects.forEach(select => {
        const value = select.value;
        if (value) {
            if (selectedIds.includes(value)) {
                select.classList.add('is-invalid');
                hasDuplicate = true;
            } else {
                select.classList.remove('is-invalid');
                selectedIds.push(value);
            }
        }
    });
    
    if (hasDuplicate) {
        Swal.fire({
            icon: 'error',
            title: 'Barang Duplikat',
            text: 'Tidak boleh memilih barang yang sama dalam satu transaksi!'
        });
    }
    
    return hasDuplicate;
}

function resetForm() {
    document.getElementById('itemsContainer').innerHTML = '';
    itemIndex = 0;
    addItemRow();
}

// Form validation before submit
document.getElementById('formStokKeluar').addEventListener('submit', function(e) {
    // Check for duplicates
    if (checkDuplicateBarang()) {
        e.preventDefault();
        return false;
    }
    
    // Check stok validation
    const jumlahInputs = document.querySelectorAll('.jumlah-input');
    let hasInvalidStok = false;
    
    jumlahInputs.forEach(input => {
        if (input.classList.contains('is-invalid')) {
            hasInvalidStok = true;
        }
    });
    
    if (hasInvalidStok) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Stok Tidak Mencukupi',
            text: 'Ada barang dengan jumlah yang melebihi stok tersedia!'
        });
        return false;
    }
    
    // Check if at least one item
    const itemRows = document.querySelectorAll('.item-row');
    if (itemRows.length === 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Tidak Ada Barang',
            text: 'Minimal harus ada 1 barang dalam transaksi!'
        });
        return false;
    }
});
</script>

<?php include '../includes/footer.php'; ?>
