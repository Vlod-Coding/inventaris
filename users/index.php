<?php
/**
 * ========================================
 * KELOLA USER
 * ========================================
 * File: users/index.php
 * Fungsi: Menampilkan daftar user dan CRUD operations
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';
require_once '../config/check_admin.php'; // Hanya administrator yang bisa akses

// Set variabel untuk template
$page_title = 'Kelola User';
$page_icon = 'users';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '../index.php'],
    ['label' => 'Kelola User']
];

// Query untuk mengambil semua user
$query = "SELECT id, username, nama_lengkap, role, created_at 
          FROM users 
          ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

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
        
        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success_message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= $_SESSION['error_message'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>
        
        <!-- Card Daftar User -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>
                    <i class="fas fa-users me-2"></i>
                    Daftar User
                </span>
                <a href="tambah.php" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-2"></i>Tambah User
                </a>
            </div>
            <div class="card-body">
                
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-datatable">
                        <thead class="table-light text-center">
                            <tr>
                                <th width="5%">No</th>
                                <th width="15%">Username</th>
                                <th width="25%">Nama Lengkap</th>
                                <th width="15%">Role</th>
                                <th width="20%">Tanggal Dibuat</th>
                                <th width="20%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (mysqli_num_rows($result) > 0):
                                $no = 1;
                                while ($row = mysqli_fetch_assoc($result)): 
                                    // Tentukan badge role
                                    $role_badge = '';
                                    switch($row['role']) {
                                        case 'owner':
                                            $role_badge = '<span class="badge bg-dark">Owner</span>';
                                            break;
                                        case 'administrator':
                                            $role_badge = '<span class="badge" style="background: #2d2d2d;">Administrator</span>';
                                            break;
                                        case 'cs':
                                            $role_badge = '<span class="badge" style="background: #666666;">CS</span>';
                                            break;
                                    }
                            ?>
                                <tr class="text-center">
                                    <td><?= $no++ ?></td>
                                    <td><strong><?= $row['username'] ?></strong></td>
                                    <td><?= $row['nama_lengkap'] ?></td>
                                    <td><?= $role_badge ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <a href="edit.php?id=<?= $row['id'] ?>" 
                                           class="btn btn-sm btn-warning"
                                           title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <?php if ($row['role'] !== 'owner' && $row['id'] != $_SESSION['user_id']): ?>
                                        <button onclick="confirmDelete(<?= $row['id'] ?>, '<?= $row['username'] ?>')" 
                                                class="btn btn-sm btn-danger"
                                                title="Hapus User">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php else: ?>
                                        <button class="btn btn-sm btn-secondary" 
                                                disabled
                                                title="Tidak dapat dihapus">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php 
                                endwhile;
                            else:
                            ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        <i class="fas fa-inbox fa-3x mb-2"></i><br>
                                        Belum ada user
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
            </div>
        </div>
        
    </div>
</div>

<!-- JavaScript untuk konfirmasi delete -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmDelete(userId, username) {
    Swal.fire({
        title: 'Hapus User?',
        html: `Apakah Anda yakin ingin menghapus user <strong>${username}</strong>?<br><small class="text-muted">Tindakan ini tidak dapat dibatalkan!</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#1a1a1a',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Redirect ke halaman hapus
            window.location.href = 'hapus.php?id=' + userId;
        }
    });
}
</script>

<?php include '../includes/footer.php'; ?>
