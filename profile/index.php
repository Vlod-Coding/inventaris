<?php
/**
 * ========================================
 * HALAMAN PROFILE ADMINISTRATOR
 * ========================================
 * File: profile/index.php
 * Fungsi: Menampilkan detail profil administrator
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
$page_title = 'Profile';
$page_icon = 'user';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '/inventaris/index.php'],
    ['label' => 'Profile']
];

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid px-4 py-3">
        
        <?php include '../includes/navbar.php'; ?>

        <!-- Profile Card -->
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-10">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user me-2"></i>Informasi Profile
                    </div>
                    <div class="card-body">
                        
                        <!-- Alert Success/Error -->
                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php
                                    if ($_GET['success'] == 'username') {
                                        echo 'Username berhasil diupdate!';
                                    } elseif ($_GET['success'] == 'password') {
                                        echo 'Password berhasil diupdate!';
                                    }
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Profile Information -->
                        <div class="text-center mb-4">
                            <div class="mb-3">
                                <i class="fas fa-user-circle" style="font-size: 6rem; color: var(--primary-color);"></i>
                            </div>
                            <h4 class="mb-0"><?= $user['username'] ?></h4>
                            <p class="text-muted"><?= ucfirst($user['role']) ?></p>
                        </div>

                        <hr>

                        <!-- Profile Details -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong><i class="fas fa-user me-2"></i>Username:</strong>
                            </div>
                            <div class="col-md-8">
                                <?= $user['username'] ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong><i class="fas fa-id-card me-2"></i>Nama Lengkap:</strong>
                            </div>
                            <div class="col-md-8">
                                <?= $user['nama_lengkap'] ?>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <strong><i class="fas fa-clock me-2"></i>Login Terakhir:</strong>
                            </div>
                            <div class="col-md-8">
                                <?= isset($_SESSION['login_time']) ? date('d/m/Y H:i:s', strtotime($_SESSION['login_time'])) : '-' ?>
                            </div>
                        </div>

                        <hr>

                        <!-- Action Buttons -->
                        <div class="text-center">
                            <a href="settings.php" class="btn btn-primary">
                                <i class="fas fa-cog me-2"></i>Pengaturan Akun
                            </a>
                            <a href="../index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>
