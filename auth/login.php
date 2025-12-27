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
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            position: relative;
            overflow: hidden;
        }
        
        /* Animated Background Clouds */
        .bg-animation {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            overflow: hidden;
        }
        
        .cloud {
            position: absolute;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 100px;
            animation: drift linear infinite;
        }
        
        .cloud::before,
        .cloud::after {
            content: '';
            position: absolute;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 100px;
        }
        
        .cloud:nth-child(1) {
            width: 200px;
            height: 60px;
            top: 10%;
            left: -250px;
            animation-duration: 35s;
        }
        
        .cloud:nth-child(1)::before {
            width: 100px;
            height: 60px;
            top: -30px;
            left: 30px;
        }
        
        .cloud:nth-child(1)::after {
            width: 120px;
            height: 60px;
            top: -20px;
            right: 30px;
        }
        
        .cloud:nth-child(2) {
            width: 150px;
            height: 50px;
            top: 30%;
            left: -200px;
            animation-duration: 45s;
            animation-delay: 5s;
        }
        
        .cloud:nth-child(2)::before {
            width: 80px;
            height: 50px;
            top: -25px;
            left: 20px;
        }
        
        .cloud:nth-child(2)::after {
            width: 90px;
            height: 50px;
            top: -15px;
            right: 20px;
        }
        
        .cloud:nth-child(3) {
            width: 180px;
            height: 55px;
            top: 50%;
            left: -220px;
            animation-duration: 40s;
            animation-delay: 10s;
        }
        
        .cloud:nth-child(3)::before {
            width: 90px;
            height: 55px;
            top: -28px;
            left: 25px;
        }
        
        .cloud:nth-child(3)::after {
            width: 100px;
            height: 55px;
            top: -18px;
            right: 25px;
        }
        
        .cloud:nth-child(4) {
            width: 160px;
            height: 48px;
            top: 70%;
            left: -200px;
            animation-duration: 38s;
            animation-delay: 15s;
        }
        
        .cloud:nth-child(4)::before {
            width: 85px;
            height: 48px;
            top: -24px;
            left: 22px;
        }
        
        .cloud:nth-child(4)::after {
            width: 95px;
            height: 48px;
            top: -16px;
            right: 22px;
        }
        
        .cloud:nth-child(5) {
            width: 140px;
            height: 45px;
            top: 85%;
            left: -180px;
            animation-duration: 42s;
            animation-delay: 20s;
        }
        
        .cloud:nth-child(5)::before {
            width: 75px;
            height: 45px;
            top: -22px;
            left: 18px;
        }
        
        .cloud:nth-child(5)::after {
            width: 85px;
            height: 45px;
            top: -14px;
            right: 18px;
        }
        
        .cloud:nth-child(6) {
            width: 170px;
            height: 52px;
            top: 20%;
            left: -220px;
            animation-duration: 50s;
            animation-delay: 8s;
        }
        
        .cloud:nth-child(6)::before {
            width: 88px;
            height: 52px;
            top: -26px;
            left: 24px;
        }
        
        .cloud:nth-child(6)::after {
            width: 98px;
            height: 52px;
            top: -17px;
            right: 24px;
        }
        
        .cloud:nth-child(7) {
            width: 130px;
            height: 42px;
            top: 60%;
            left: -180px;
            animation-duration: 36s;
            animation-delay: 12s;
        }
        
        .cloud:nth-child(7)::before {
            width: 70px;
            height: 42px;
            top: -21px;
            left: 16px;
        }
        
        .cloud:nth-child(7)::after {
            width: 80px;
            height: 42px;
            top: -13px;
            right: 16px;
        }
        
        .cloud:nth-child(8) {
            width: 190px;
            height: 58px;
            top: 40%;
            left: -240px;
            animation-duration: 48s;
            animation-delay: 25s;
        }
        
        .cloud:nth-child(8)::before {
            width: 95px;
            height: 58px;
            top: -29px;
            left: 28px;
        }
        
        .cloud:nth-child(8)::after {
            width: 105px;
            height: 58px;
            top: -19px;
            right: 28px;
        }
        
        .cloud:nth-child(9) {
            width: 155px;
            height: 47px;
            top: 75%;
            left: -200px;
            animation-duration: 41s;
            animation-delay: 18s;
        }
        
        .cloud:nth-child(9)::before {
            width: 82px;
            height: 47px;
            top: -23px;
            left: 21px;
        }
        
        .cloud:nth-child(9)::after {
            width: 92px;
            height: 47px;
            top: -15px;
            right: 21px;
        }
        
        .cloud:nth-child(10) {
            width: 145px;
            height: 44px;
            top: 55%;
            left: -190px;
            animation-duration: 39s;
            animation-delay: 30s;
        }
        
        .cloud:nth-child(10)::before {
            width: 77px;
            height: 44px;
            top: -22px;
            left: 19px;
        }
        
        .cloud:nth-child(10)::after {
            width: 87px;
            height: 44px;
            top: -14px;
            right: 19px;
        }
        
        /* Clouds moving from right to left */
        .cloud:nth-child(2),
        .cloud:nth-child(5),
        .cloud:nth-child(7),
        .cloud:nth-child(9) {
            animation-name: drift-reverse;
            left: auto;
            right: -250px;
        }
        
        @keyframes drift {
            0% {
                left: -250px;
            }
            100% {
                left: 110%;
            }
        }
        
        @keyframes drift-reverse {
            0% {
                right: -250px;
            }
            100% {
                right: 110%;
            }
        }
        
        /* Loading Screen */
        .loading-screen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }
        
        .loading-screen.fade-out {
            opacity: 0;
            visibility: hidden;
        }
        
        .warehouse-box {
            position: relative;
            width: 120px;
            height: 120px;
            margin-bottom: 30px;
        }
        
        .box-bottom {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 120px;
            height: 60px;
            background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
            border: 3px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
        }
        
        .box-top {
            position: absolute;
            top: 0;
            left: 0;
            width: 120px;
            height: 60px;
            background: linear-gradient(135deg, #3d3d3d 0%, #2d2d2d 100%);
            border: 3px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            transform-origin: bottom;
            animation: box-open 2s ease-in-out infinite;
        }
        
        .box-icon {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 2.5rem;
            color: white;
            opacity: 0;
            animation: icon-appear 2s ease-in-out infinite;
        }
        
        @keyframes box-open {
            0%, 40% {
                transform: rotateX(0deg);
            }
            50%, 90% {
                transform: rotateX(-120deg);
            }
            100% {
                transform: rotateX(0deg);
            }
        }
        
        @keyframes icon-appear {
            0%, 40% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.5);
            }
            50%, 90% {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1);
            }
            100% {
                opacity: 0;
                transform: translate(-50%, -50%) scale(0.5);
            }
        }
        
        .loading-text {
            color: white;
            font-size: 1.2rem;
            font-weight: 600;
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 0.6;
            }
            50% {
                opacity: 1;
            }
        }
        
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            position: relative;
            z-index: 10;
        }
        .login-header {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .login-header i {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        .login-body {
            padding: 40px;
        }
        .form-control:focus {
            border-color: #1a1a1a;
            box-shadow: 0 0 0 0.2rem rgba(0, 0, 0, 0.1);
        }
        .btn-login {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            background: linear-gradient(135deg, #2d2d2d 0%, #3d3d3d 100%);
        }
        .default-login-info {
            background: #f5f5f5;
            border-left: 4px solid #1a1a1a;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
        }
        
        /* Mobile Responsive */
        @media (max-width: 576px) {
            .login-card {
                max-width: 90%;
                margin: 0 auto;
                border-radius: 16px;
            }
            
            .login-header {
                padding: 20px 15px;
            }
            
            .login-header i {
                font-size: 2rem;
                margin-bottom: 8px;
            }
            
            .login-header h3 {
                font-size: 1.3rem;
                margin-bottom: 5px;
            }
            
            .login-header small {
                font-size: 0.8rem;
            }
            
            .login-body {
                padding: 25px 20px;
            }
            
            .form-label {
                font-size: 0.9rem;
                margin-bottom: 6px;
            }
            
            .form-control {
                padding: 10px 12px;
                font-size: 0.95rem;
            }
            
            .btn-login {
                padding: 10px;
                font-size: 0.95rem;
            }
            
            .default-login-info {
                padding: 12px;
                font-size: 0.85rem;
                margin-top: 15px;
            }
            
            .warehouse-box {
                width: 80px;
                height: 80px;
                margin-bottom: 20px;
            }
            
            .box-bottom,
            .box-top {
                width: 80px;
                height: 40px;
            }
            
            .box-icon {
                font-size: 1.8rem;
            }
            
            .loading-text {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>

<!-- Loading Screen -->
<div class="loading-screen" id="loadingScreen">
    <div class="warehouse-box">
        <div class="box-bottom"></div>
        <div class="box-top"></div>
        <div class="box-icon">
            <i class="fas fa-box-open"></i>
        </div>
    </div>
    <div class="loading-text">Sistem Inventaris</div>
</div>

<!-- Animated Background -->
<div class="bg-animation">
    <div class="cloud"></div>
    <div class="cloud"></div>
    <div class="cloud"></div>
    <div class="cloud"></div>
    <div class="cloud"></div>
    <div class="cloud"></div>
    <div class="cloud"></div>
    <div class="cloud"></div>
    <div class="cloud"></div>
    <div class="cloud"></div>
</div>

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
    // Loading Screen Control
    window.addEventListener('load', function() {
        const loadingScreen = document.getElementById('loadingScreen');
        
        // Hide loading screen after 2.5 seconds
        setTimeout(function() {
            loadingScreen.classList.add('fade-out');
        }, 2500);
    });
    
    // Show error popup if there's an error
    <?php if ($error): ?>
        // Wait for loading screen to finish before showing error
        setTimeout(function() {
            Swal.fire({
                icon: 'error',
                title: 'Login Gagal!',
                text: '<?= $error ?>',
                confirmButtonText: 'OK',
                confirmButtonColor: '#1a1a1a',
                backdrop: true,
                allowOutsideClick: true
            });
        }, 3000);
    <?php endif; ?>
</script>

</body>
</html>