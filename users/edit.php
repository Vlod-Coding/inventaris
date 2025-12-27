<?php
/**
 * ========================================
 * EDIT USER
 * ========================================
 * File: users/edit.php
 * Fungsi: Form untuk mengedit data user
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';
require_once '../config/check_admin.php';

// Cek apakah ada parameter ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = 'ID user tidak valid!';
    header('Location: index.php');
    exit;
}

$user_id = (int)$_GET['id'];

// Ambil data user
$query = "SELECT id, username, nama_lengkap, role FROM users WHERE id = $user_id LIMIT 1";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 0) {
    $_SESSION['error_message'] = 'User tidak ditemukan!';
    header('Location: index.php');
    exit;
}

$user = mysqli_fetch_assoc($result);

// Set variabel untuk template
$page_title = 'Edit User';
$page_icon = 'user-edit';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '../index.php'],
    ['label' => 'Kelola User', 'url' => 'index.php'],
    ['label' => 'Edit User']
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
                <i class="fas fa-user-edit me-2"></i>
                Form Edit User: <strong><?= $user['username'] ?></strong>
            </div>
            <div class="card-body">
                
                <form action="proses_edit.php" method="POST" id="formEditUser">
                    <input type="hidden" name="id" value="<?= $user['id'] ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       name="username" 
                                       required
                                       value="<?= $user['username'] ?>"
                                       placeholder="Masukkan username">
                                <small class="text-muted">Username harus unik</small>
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
                                       value="<?= $user['nama_lengkap'] ?>"
                                       placeholder="Masukkan nama lengkap">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password" 
                                       minlength="6"
                                       placeholder="Kosongkan jika tidak ingin mengubah password">
                                <small class="text-muted">Minimal 6 karakter. Kosongkan jika tidak ingin mengubah.</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password_confirm" 
                                       name="password_confirm" 
                                       minlength="6"
                                       placeholder="Ulangi password baru">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">-- Pilih Role --</option>
                                    <option value="owner" <?= $user['role'] == 'owner' ? 'selected' : '' ?>>Owner</option>
                                    <option value="administrator" <?= $user['role'] == 'administrator' ? 'selected' : '' ?>>Administrator</option>
                                    <option value="cs" <?= $user['role'] == 'cs' ? 'selected' : '' ?>>CS (Customer Service)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update
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
// Validasi password match (jika diisi)
document.getElementById('formEditUser').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const passwordConfirm = document.getElementById('password_confirm').value;
    
    // Hanya validasi jika password diisi
    if (password || passwordConfirm) {
        if (password !== passwordConfirm) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Password Tidak Cocok!',
                text: 'Password dan konfirmasi password harus sama.',
                confirmButtonColor: '#1a1a1a'
            });
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>
