<?php
/**
 * ========================================
 * HALAMAN LOG AKTIVITAS
 * ========================================
 * File: logs/index.php
 * Fungsi: Menampilkan log aktivitas user
 */

session_start();

// Cek login
if (!isset($_SESSION['login'])) {
    header('Location: ../auth/login.php?error=2');
    exit;
}

require_once '../config/koneksi.php';
require_once '../config/log_helper.php';

// Setup page variables
$page_title = 'Log Aktivitas';
$page_icon = 'history';
$breadcrumb = [
    ['label' => 'Dashboard', 'url' => '/inventaris/index.php'],
    ['label' => 'Log Aktivitas']
];

// Get filter parameters
$filter_user = isset($_GET['user']) ? escape($_GET['user']) : '';
$filter_action = isset($_GET['action']) ? escape($_GET['action']) : '';
$filter_module = isset($_GET['module']) ? escape($_GET['module']) : '';
$filter_date_start = isset($_GET['date_start']) ? escape($_GET['date_start']) : '';
$filter_date_end = isset($_GET['date_end']) ? escape($_GET['date_end']) : '';

// Build query
$query = "SELECT al.*, u.nama_lengkap 
          FROM activity_logs al
          LEFT JOIN users u ON al.user_id = u.id
          WHERE 1=1";

if (!empty($filter_user)) {
    $query .= " AND al.username LIKE '%$filter_user%'";
}
if (!empty($filter_action)) {
    $query .= " AND al.action = '$filter_action'";
}
if (!empty($filter_module)) {
    $query .= " AND al.module = '$filter_module'";
}
if (!empty($filter_date_start)) {
    $query .= " AND DATE(al.created_at) >= '$filter_date_start'";
}
if (!empty($filter_date_end)) {
    $query .= " AND DATE(al.created_at) <= '$filter_date_end'";
}

$query .= " ORDER BY al.created_at DESC";

$result = mysqli_query($conn, $query);

// Get all users for filter
$users_query = "SELECT DISTINCT username FROM activity_logs ORDER BY username";
$users_result = mysqli_query($conn, $users_query);

include '../includes/header.php';
include '../includes/sidebar.php';
?>

<div class="content-wrapper">
    <div class="container-fluid px-4 py-3">
        
        <?php include '../includes/navbar.php'; ?>

        <!-- Filter Card -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-filter me-2"></i>Filter Log Aktivitas
            </div>
            <div class="card-body">
                <form method="GET" action="">
                    <div class="row g-3">
                        <!-- Filter User -->
                        <div class="col-md-3">
                            <label class="form-label">User</label>
                            <select name="user" class="form-select">
                                <option value="">Semua User</option>
                                <?php while($user = mysqli_fetch_assoc($users_result)): ?>
                                    <option value="<?= $user['username'] ?>" 
                                            <?= ($filter_user == $user['username']) ? 'selected' : '' ?>>
                                        <?= $user['username'] ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <!-- Filter Action -->
                        <div class="col-md-3">
                            <label class="form-label">Action</label>
                            <select name="action" class="form-select">
                                <option value="">Semua Action</option>
                                <?php foreach(get_log_actions() as $key => $value): ?>
                                    <option value="<?= $key ?>" 
                                            <?= ($filter_action == $key) ? 'selected' : '' ?>>
                                        <?= $value ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Filter Module -->
                        <div class="col-md-3">
                            <label class="form-label">Module</label>
                            <select name="module" class="form-select">
                                <option value="">Semua Module</option>
                                <?php foreach(get_log_modules() as $key => $value): ?>
                                    <option value="<?= $key ?>" 
                                            <?= ($filter_module == $key) ? 'selected' : '' ?>>
                                        <?= $value ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Filter Date Start -->
                        <div class="col-md-3">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="date_start" class="form-control" 
                                   value="<?= $filter_date_start ?>">
                        </div>
                        
                        <!-- Filter Date End -->
                        <div class="col-md-3">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="date_end" class="form-control" 
                                   value="<?= $filter_date_end ?>">
                        </div>
                        
                        <!-- Buttons -->
                        <div class="col-md-9">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>Filter
                                </button>
                                <a href="index.php" class="btn btn-secondary">
                                    <i class="fas fa-redo me-2"></i>Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Logs Table Card -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-list me-2"></i>Daftar Log Aktivitas
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-datatable">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="12%">User</th>
                                <th width="12%">Action</th>
                                <th width="15%">Module</th>
                                <th width="38%">Deskripsi</th>
                                <th width="18%">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (mysqli_num_rows($result) > 0): ?>
                                <?php $no = 1; while($log = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>
                                            <strong><?= $log['username'] ?></strong>
                                            <?php if (!empty($log['nama_lengkap'])): ?>
                                                <br><small class="text-muted"><?= $log['nama_lengkap'] ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badge_class = 'secondary';
                                            switch($log['action']) {
                                                case 'LOGIN': $badge_class = 'success'; break;
                                                case 'LOGOUT': $badge_class = 'info'; break;
                                                case 'LOGIN_FAILED': $badge_class = 'danger'; break;
                                                case 'CREATE': $badge_class = 'primary'; break;
                                                case 'UPDATE': $badge_class = 'warning'; break;
                                                case 'DELETE': $badge_class = 'danger'; break;
                                            }
                                            ?>
                                            <span class="badge bg-<?= $badge_class ?>">
                                                <?= $log['action'] ?>
                                            </span>
                                        </td>
                                        <td><?= $log['module'] ?></td>
                                        <td><?= $log['description'] ?></td>
                                        <td>
                                            <small>
                                                <i class="fas fa-calendar me-1"></i>
                                                <?= date('d/m/Y', strtotime($log['created_at'])) ?>
                                                <br>
                                                <i class="fas fa-clock me-1"></i>
                                                <?= date('H:i:s', strtotime($log['created_at'])) ?>
                                            </small>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-info-circle fa-2x text-muted mb-2"></i>
                                        <p class="mb-0">Tidak ada log aktivitas yang ditemukan</p>
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

<?php include '../includes/footer.php'; ?>
