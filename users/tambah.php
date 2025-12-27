<?php
/**
 * ========================================
 * TAMBAH USER
 * ========================================
 * File: users/tambah.php
 * Fungsi: Form untuk menambah user baru
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';
require_once '../config/check_admin.php';

// Set variabel untuk template
$page_title = 'Tambah User';
$page_icon = 'user-plus';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '../index.php'],
    ['label' => 'Kelola User', 'url' => 'index.php'],
    ['label' => 'Tambah User']
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
        
        <!-- Card Form -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-user-plus me-2"></i>
                Form Tambah User
            </div>
            <div class="card-body">
                
                <form action="proses_tambah.php" method="POST" id="formTambahUser">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       name="username" 
                                       required
                                       placeholder="Masukkan username"
                                       autocomplete="off">
                                <small class="text-muted">Username harus unik dan tidak boleh sama dengan user lain</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="nama_lengkap" 
                                       name="nama_lengkap" 
                                       required
                                       placeholder="Masukkan nama lengkap">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       required
                                       minlength="6"
                                       placeholder="Minimal 6 karakter">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirm" 
                                       name="password_confirm" 
                                       required
                                       minlength="6"
                                       placeholder="Ulangi password">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="owner">Owner</option>
                                    <option value="administrator">Administrator</option>
                                    <option value="cs">CS (Customer Service)</option>
                                </select>
                                <small class="text-muted">
                                    <strong>Owner:</strong> Akses penuh<br>
                                    <strong>Administrator:</strong> Dapat kelola user<br>
                                    <strong>CS:</strong> Akses terbatas
                                </small>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan
                        </button>
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    </div>
                    
                </form>
                
            </div>
        </div>
        
    </div>
</div>

<script>
// Validasi password match
document.getElementById('formTambahUser').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    
    if (password !== passwordConfirm) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Password Tidak Cocok!',
            text: 'Password dan konfirmasi password harus sama.',
            confirmButtonColor: '#1a1a1a'
        });
    }
});
</script>

<?php include '../includes/footer.php'; ?>
