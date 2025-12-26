<?php
/**
 * ========================================
 * HALAMAN SETTINGS ADMINISTRATOR
 * ========================================
 * File: profile/settings.php
 * Fungsi: Mengubah username dan password
 */

session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    header('Location: ../auth/login.php?error=2');
    exit;
}

require_once '../config/koneksi.php';

// Ambil data user dari database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = '$user_id' LIMIT 1";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);

// Setup page variables
$page_title = 'Settings';
$page_icon = 'cog';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '/inventaris/index.php'],
    ['label' => 'Profile', 'url' => '/inventaris/profile/index.php'],
    ['label' => 'Settings']
];

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid px-4 py-3">
        
        <?php include '../includes/navbar.php'; ?>

        <div class="row">
            
            <!-- Update Username Form -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user me-2"></i>Update Username
                    </div>
                    <div class="card-body">
                        
                        <!-- Alert Messages -->
                        <?php if (isset($_GET['error']) && $_GET['error'] == 'username_empty'): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>Username tidak boleh kosong!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['error']) && $_GET['error'] == 'username_short'): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>Username minimal 3 karakter!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['error']) && $_GET['error'] == 'username_exists'): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>Username sudah digunakan!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['success']) && $_GET['success'] == 'username'): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>Username berhasil diupdate!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="update_username.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Username Saat Ini</label>
                                <input type="text" class="form-control" value="<?= $user['username'] ?>" disabled>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Username Baru <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control" 
                                       placeholder="Masukkan username baru" required>
                                <small class="text-muted">Minimal 3 karakter</small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Username
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Update Password Form -->
            <div class="col-lg-6 col-md-12 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-lock me-2"></i>Update Password
                    </div>
                    <div class="card-body">
                        
                        <!-- Alert Messages -->
                        <?php if (isset($_GET['error']) && $_GET['error'] == 'password_wrong'): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>Password lama salah!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['error']) && $_GET['error'] == 'password_mismatch'): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>Konfirmasi password tidak cocok!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['error']) && $_GET['error'] == 'password_short'): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>Password minimal 6 karakter!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_GET['success']) && $_GET['success'] == 'password'): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>Password berhasil diupdate!
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="update_password.php" method="POST" id="passwordForm">
                            <div class="mb-3">
                                <label class="form-label">Password Lama <span class="text-danger">*</span></label>
                                <input type="password" name="old_password" class="form-control" 
                                       placeholder="Masukkan password lama" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Password Baru <span class="text-danger">*</span></label>
                                <input type="password" name="new_password" id="newPassword" class="form-control" 
                                       placeholder="Masukkan password baru" required>
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                                <input type="password" name="confirm_password" id="confirmPassword" class="form-control" 
                                       placeholder="Konfirmasi password baru" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Password
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali
                            </a>
                        </form>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<!-- Client-side validation for password match -->
<script>
document.getElementById('passwordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('Konfirmasi password tidak cocok!');
    }
});
</script>

<?php include '../includes/footer.php'; ?>
