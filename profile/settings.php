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

        <!-- Unified Update Form -->
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-user-cog me-2"></i>Update Account Settings
                    </div>
                    <div class="card-body">
                        
                        <!-- Alert Messages -->
                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <?php
                                    if ($_GET['error'] == 'username_short') echo 'Username minimal 3 karakter!';
                                    elseif ($_GET['error'] == 'username_exists') echo 'Username sudah digunakan!';
                                    elseif ($_GET['error'] == 'password_wrong') echo 'Password lama salah!';
                                    elseif ($_GET['error'] == 'password_mismatch') echo 'Konfirmasi password tidak cocok!';
                                    elseif ($_GET['error'] == 'password_short') echo 'Password minimal 6 karakter!';
                                    elseif ($_GET['error'] == 'no_changes') echo 'Tidak ada perubahan yang dilakukan!';
                                    else echo 'Terjadi kesalahan!';
                                ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <form action="update_account.php" method="POST" id="accountForm">
                            
                            <!-- Username Section -->
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-user me-2"></i>Update Username (Opsional)
                                </h6>
                                
                                <div class="mb-3">
                                    <label class="form-label">Username Saat Ini</label>
                                    <input type="text" class="form-control" value="<?= $user['username'] ?>" disabled>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Username Baru</label>
                                    <input type="text" name="new_username" id="newUsername" class="form-control" 
                                           placeholder="Kosongkan jika tidak ingin mengubah username">
                                    <small class="text-muted">Minimal 3 karakter (kosongkan jika tidak ingin diubah)</small>
                                </div>
                            </div>

                            <!-- Password Section -->
                            <div class="mb-4">
                                <h6 class="border-bottom pb-2 mb-3">
                                    <i class="fas fa-lock me-2"></i>Update Password (Opsional)
                                </h6>
                                
                                <div class="mb-3">
                                    <label class="form-label">Password Lama</label>
                                    <div class="position-relative">
                                        <input type="password" name="old_password" id="oldPassword" class="form-control pe-5" 
                                               placeholder="Wajib diisi jika ingin mengubah password"
                                               oninput="toggleEyeIcon('oldPassword', 'eyeOld')">
                                        <button class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent" 
                                                type="button" id="eyeOld" style="display: none;"
                                                onclick="togglePassword('oldPassword', this)">
                                            <i class="fas fa-eye text-muted"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Wajib diisi jika ingin mengubah password</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Password Baru</label>
                                    <div class="position-relative">
                                        <input type="password" name="new_password" id="newPassword" class="form-control pe-5" 
                                               placeholder="Kosongkan jika tidak ingin mengubah password"
                                               oninput="toggleEyeIcon('newPassword', 'eyeNew')">
                                        <button class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent" 
                                                type="button" id="eyeNew" style="display: none;"
                                                onclick="togglePassword('newPassword', this)">
                                            <i class="fas fa-eye text-muted"></i>
                                        </button>
                                    </div>
                                    <small class="text-muted">Minimal 6 karakter (kosongkan jika tidak ingin diubah)</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Konfirmasi Password Baru</label>
                                    <div class="position-relative">
                                        <input type="password" name="confirm_password" id="confirmPassword" class="form-control pe-5" 
                                               placeholder="Konfirmasi password baru"
                                               oninput="toggleEyeIcon('confirmPassword', 'eyeConfirm')">
                                        <button class="btn position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent" 
                                                type="button" id="eyeConfirm" style="display: none;"
                                                onclick="togglePassword('confirmPassword', this)">
                                            <i class="fas fa-eye text-muted"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save me-2"></i>Update Account
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- Confirmation Popup with SweetAlert2 -->
<script>
// Konfirmasi untuk Update Account (Username dan/atau Password)
document.getElementById('accountForm').addEventListener('submit', function(e) {
    e.preventDefault(); // Prevent default submit
    
    const newUsername = document.getElementById('newUsername').value.trim();
    const oldPassword = document.getElementById('oldPassword').value.trim();
    const newPassword = document.getElementById('newPassword').value.trim();
    const confirmPassword = document.getElementById('confirmPassword').value.trim();
    const currentUsername = '<?= $user['username'] ?>';
    
    // Deteksi apa yang akan diupdate
    const updateUsername = newUsername.length > 0;
    const updatePassword = newPassword.length > 0 || oldPassword.length > 0;
    
    // Validasi: Harus ada minimal 1 perubahan
    if (!updateUsername && !updatePassword) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Ada Perubahan',
            text: 'Silakan isi minimal satu field untuk melakukan perubahan!',
            confirmButtonColor: '#1a1a1a'
        });
        return;
    }
    
    // Validasi Username
    if (updateUsername && newUsername.length < 3) {
        Swal.fire({
            icon: 'error',
            title: 'Username Terlalu Pendek',
            text: 'Username minimal 3 karakter!',
            confirmButtonColor: '#1a1a1a'
        });
        return;
    }
    
    // Validasi Password
    if (updatePassword) {
        // Cek semua field password terisi
        if (!oldPassword || !newPassword || !confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Field Password Tidak Lengkap',
                text: 'Jika ingin mengubah password, semua field password harus diisi!',
                confirmButtonColor: '#1a1a1a'
            });
            return;
        }
        
        // Validasi password match
        if (newPassword !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: 'Password Tidak Cocok',
                text: 'Konfirmasi password tidak cocok dengan password baru!',
                confirmButtonColor: '#1a1a1a'
            });
            return;
        }
        
        // Validasi minimal 6 karakter
        if (newPassword.length < 6) {
            Swal.fire({
                icon: 'error',
                title: 'Password Terlalu Pendek',
                text: 'Password minimal 6 karakter!',
                confirmButtonColor: '#1a1a1a'
            });
            return;
        }
    }
    
    // Buat pesan konfirmasi dinamis
    let title = 'Konfirmasi Perubahan ';
    let htmlContent = '<div style="text-align: left; padding: 10px;">';
    
    if (updateUsername && updatePassword) {
        title += 'Username & Password';
        htmlContent += `
            <p><strong>Username Saat Ini:</strong> ${currentUsername}</p>
            <p><strong>Username Baru:</strong> ${newUsername}</p>
            <p><i class="fas fa-lock"></i> Password akan diubah</p>
        `;
    } else if (updateUsername) {
        title += 'Username';
        htmlContent += `
            <p><strong>Username Saat Ini:</strong> ${currentUsername}</p>
            <p><strong>Username Baru:</strong> ${newUsername}</p>
        `;
    } else {
        title += 'Password';
        htmlContent += `
            <p><i class="fas fa-lock"></i> Password baru akan menggantikan password lama Anda.</p>
        `;
    }
    
    htmlContent += `
        <hr>
        <p class="text-muted" style="font-size: 0.9em;">
            <i class="fas fa-exclamation-triangle"></i> 
            Anda akan logout otomatis setelah perubahan.
        </p>
    `;
    
    if (updatePassword) {
        htmlContent += `
            <p class="text-muted" style="font-size: 0.9em;">
                <i class="fas fa-info-circle"></i> 
                Pastikan Anda mengingat kredensial baru untuk login berikutnya.
            </p>
        `;
    }
    
    htmlContent += '</div>';
    
    // Konfirmasi perubahan
    Swal.fire({
        title: title,
        html: htmlContent,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#1a1a1a',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Update!',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit form jika user konfirmasi
            e.target.submit();
        }
    });
});

// Toggle Eye Icon Visibility based on input value
function toggleEyeIcon(inputId, eyeId) {
    const input = document.getElementById(inputId);
    const eyeButton = document.getElementById(eyeId);
    
    if (input.value.length > 0) {
        eyeButton.style.display = 'block';
    } else {
        eyeButton.style.display = 'none';
        // Reset to password type when empty
        input.type = 'password';
        const icon = eyeButton.querySelector('i');
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Toggle Password Visibility
function togglePassword(inputId, button) {
    const input = document.getElementById(inputId);
    const icon = button.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

<?php include '../includes/footer.php'; ?>
