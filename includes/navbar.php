<?php
/**
 * ========================================
 * TOP NAVBAR
 * ========================================
 * File: includes/navbar.php
 * Fungsi: Navbar atas untuk breadcrumb dan info user
 */
?>

<nav class="top-navbar">
    <div class="d-flex justify-content-between align-items-center">
        <!-- Left Section: Hamburger + Page Title -->
        <div class="d-flex align-items-center">
            <!-- Hamburger Menu Button (integrated in<?php
// Deteksi base path untuk support localhost dan production
$base_path = '';
if (strpos($_SERVER['REQUEST_URI'], '/inventaris/') !== false) {
    $base_path = '/inventaris';
}
?>

<!-- Navbar -->
            <button class="hamburger-btn-navbar me-3" id="hamburgerBtn" aria-label="Toggle Menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
            
            <!-- Page Title -->
            <div>
                <h5 class="mb-0">
                    <i class="fas fa-<?= isset($page_icon) ? $page_icon : 'home' ?> me-2"></i>
                    <?= isset($page_title) ? $page_title : 'Dashboard' ?>
                </h5>
                <?php if (isset($breadcrumb)): ?>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 mt-2">
                            <?php foreach ($breadcrumb as $item): ?>
                                <?php if (isset($item['url'])): ?>
                                    <li class="breadcrumb-item">
                                        <a href="<?= $item['url'] ?>"><?= $item['label'] ?></a>
                                    </li>
                                <?php else: ?>
                                    <li class="breadcrumb-item active"><?= $item['label'] ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ol>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Right Section: Clock + User Info -->
        <div class="d-flex align-items-center">
            <!-- Clock -->
            <div class="me-4 text-muted d-none d-md-block">
                <i class="fas fa-clock me-2"></i>
                <span id="clock"><?= date('H:i:s') ?></span>
                <span class="ms-2"><?= date('d/m/Y') ?></span>
            </div>
            
            <!-- User Dropdown -->
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" type="button" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle me-2"></i>
                    <span class="d-none d-sm-inline"><?= $_SESSION['username'] ?></span>
                    <span class="d-inline d-sm-none"><?= $_SESSION['username'] ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="<?= $base_path ?>/profile/index.php">
                            <i class="fas fa-user me-2"></i>Profile
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= $base_path ?>/profile/settings.php">
                            <i class="fas fa-cog me-2"></i>Settings
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" 
                           href="#"
                           onclick="showLogoutModal(); return false;">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<script>
// Update clock setiap detik
function updateClock() {
    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    document.getElementById('clock').textContent = `${hours}:${minutes}:${seconds}`;
}
setInterval(updateClock, 1000);
</script>