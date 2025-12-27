<?php
/**
 * ========================================
 * HALAMAN LOGIN
 * ========================================
 * File: auth/login.php
 * Fungsi: Form login untuk autentikasi user
 */

session_start();

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['login'])) {
    header('Location: ../index.php');
    exit;
}

// Cek jika ada pesan error
$error = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] == 1) {
        $error = 'Username atau Password salah!';
    } elseif ($_GET['error'] == 2) {
        $error = 'Silakan login terlebih dahulu!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Inventaris</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-body {
            padding: 40px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px;
            font-weight: bold;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="login-card mx-auto">
        <!-- Header -->
        <div class="login-header">
            <i class="fas fa-box-open fa-3x mb-3"></i>
            <h3 class="mb-0">Sistem Inventaris</h3>
            <p class="mb-0 mt-2">Silakan login untuk melanjutkan</p>
        </div>

        <!-- Body -->
        <div class="login-body">
            <form action="proses_login.php" method="POST">
                <!-- Username -->
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-user me-2"></i>Username
                    </label>
                    <input type="text" name="username" class="form-control" 
                           placeholder="Masukkan username" required autofocus>
                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label class="form-label">
                        <i class="fas fa-lock me-2"></i>Password
                    </label>
                    <input type="password" name="password" class="form-control" 
                           placeholder="Masukkan password" required>
                </div>

                <!-- Button Login -->
                <button type="submit" class="btn btn-primary btn-login w-100">
                    <i class="fas fa-sign-in-alt me-2"></i>LOGIN
                </button>
            </form>

            <!-- Info Default Login -->
            <div class="alert alert-info mt-4 mb-0">
                <small>
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Default Login:</strong><br>
                    Username: <code>admin</code><br>
                    Password: <code>admin123</code>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Show error popup if there's an error
    <?php if ($error): ?>
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal!',
            text: '<?= $error ?>',
            confirmButtonText: 'OK',
            confirmButtonColor: '#667eea',
            backdrop: true,
            allowOutsideClick: true
        });
    <?php endif; ?>
</script>

</body>
</html>