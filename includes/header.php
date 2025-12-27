<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Sistem Inventaris</title>
    
    <!-- Google Fonts - Inter (Modern, Clean) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #1a1a1a;
            --secondary-color: #2d2d2d;
            --sidebar-width: 250px;
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }
        
        * {
            font-family: var(--font-family);
        }
        
        body {
            font-family: var(--font-family);
            background-color: #f5f5f5;
            font-weight: 400;
            letter-spacing: -0.01em;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        h1, h2, h3, h4, h5, h6 {
            font-weight: 600;
            letter-spacing: -0.02em;
        }
        
        .btn {
            font-weight: 500;
            letter-spacing: -0.01em;
        }
        
        .nav-link {
            font-weight: 500;
        }
        
        /* Hamburger Menu Button - Navbar Integrated */
        .hamburger-btn-navbar {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border: none;
            width: 45px;
            height: 45px;
            border-radius: 8px;
            cursor: pointer;
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 4px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        
        .hamburger-btn-navbar:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, #2d2d2d 0%, #3d3d3d 100%);
        }
        
        .hamburger-btn-navbar:active {
            transform: translateY(0);
        }
        
        .hamburger-btn-navbar span {
            width: 22px;
            height: 2.5px;
            background: white;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .hamburger-btn-navbar.active span:nth-child(1) {
            transform: rotate(45deg) translate(6px, 6px);
        }
        
        .hamburger-btn-navbar.active span:nth-child(2) {
            opacity: 0;
            transform: translateX(-10px);
        }
        
        .hamburger-btn-navbar.active span:nth-child(3) {
            transform: rotate(-45deg) translate(6px, -6px);
        }
        
        /* Sidebar Overlay */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s;
        }
        
        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1a1a1a 0%, #0d0d0d 100%);
            color: white;
            transition: all 0.3s;
            z-index: 1000;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
        }
        
        .sidebar .logo {
            text-align: center;
            padding: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar .logo i {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 15px 25px;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left-color: white;
        }
        
        .sidebar .nav-link i {
            width: 25px;
            margin-right: 10px;
        }
        
        /* Content Area */
        .content-wrapper {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s;
        }
        
        /* Navbar */
        .top-navbar {
            background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            padding: 16px 30px;
            margin-bottom: 30px;
            border-radius: 0 0 12px 12px;
        }
        
        .top-navbar h5 {
            font-weight: 600;
            margin-bottom: 0;
            color: #1a1a1a;
        }
        
        /* Breadcrumb */
        .breadcrumb {
            font-size: 0.875rem;
            background: transparent;
            padding: 0;
        }
        
        .breadcrumb-item a {
            color: #2d2d2d;
            text-decoration: none;
            font-weight: 500;
        }
        
        .breadcrumb-item a:hover {
            color: #000000;
        }
        
        .breadcrumb-item.active {
            color: #6c757d;
            font-weight: 400;
        }
        
        /* Dropdown */
        .dropdown-menu {
            border: 1px solid rgba(0,0,0,0.08);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            border-radius: 8px;
            padding: 8px;
        }
        
        .dropdown-item {
            border-radius: 6px;
            padding: 8px 12px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .dropdown-item:hover {
            background-color: #f5f5f5;
            color: #1a1a1a;
        }
        
        /* Form Inputs */
        .form-control, .form-select {
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.9375rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: #1a1a1a;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        }
        
        .form-label {
            font-weight: 500;
            color: #4a5568;
            margin-bottom: 8px;
        }
        
        /* Card Styles */
        .card {
            border: 1px solid rgba(0,0,0,0.06);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }
        
        .card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transform: translateY(-2px);
        }
        
        .card-header {
            background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
            color: white;
            border-radius: 12px 12px 0 0 !important;
            font-weight: 600;
            padding: 16px 20px;
            letter-spacing: -0.01em;
        }
        
        /* Stat Cards */
        .stat-card {
            border-left: 4px solid;
            transition: all 0.3s ease;
            border-radius: 12px;
        }
        
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }
        
        .stat-card.blue { border-left-color: #2d2d2d; }
        .stat-card.green { border-left-color: #4a4a4a; }
        .stat-card.orange { border-left-color: #666666; }
        .stat-card.red { border-left-color: #1a1a1a; }
        
        /* Buttons */
        .btn {
            border-radius: 8px;
            padding: 8px 16px;
            transition: all 0.3s ease;
            font-weight: 500;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border: none;
            color: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #2d2d2d 0%, #3d3d3d 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        .btn-sm {
            border-radius: 6px;
            font-size: 0.875rem;
        }
        
        .btn-info {
            background: linear-gradient(135deg, #4a4a4a 0%, #5a5a5a 100%);
            border: none;
            color: white;
        }
        
        .btn-info:hover {
            background: linear-gradient(135deg, #2d2d2d 0%, #3d3d3d 100%);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #666666 0%, #777777 100%);
            border: none;
            color: white;
        }
        
        .btn-warning:hover {
            background: linear-gradient(135deg, #4a4a4a 0%, #5a5a5a 100%);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%);
            border: none;
            color: white;
        }
        
        .btn-danger:hover {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
        }
        
        .btn-success {
            background: linear-gradient(135deg, #2d2d2d 0%, #3d3d3d 100%);
            border: none;
            color: white;
        }
        
        .btn-success:hover {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        }
        
        /* Tables */
        .table {
            background: white;
            font-size: 0.9375rem;
        }
        
        .table thead th {
            font-weight: 600;
            letter-spacing: -0.01em;
            border-bottom: 2px solid #e5e5e5;
            padding: 14px 12px;
            background: linear-gradient(180deg, #fafafa 0%, #f5f5f5 100%);
        }
        
        .table tbody td {
            padding: 12px;
            vertical-align: middle;
        }
        
        .table-hover tbody tr:hover {
            background-color: #f5f5f5;
        }
        
        /* Badges */
        .badge {
            font-weight: 500;
            padding: 6px 12px;
            border-radius: 6px;
            letter-spacing: -0.01em;
        }
        
        /* Logout Modal Styles */
        .logout-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: none;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .logout-modal-overlay.show {
            display: flex;
            opacity: 1;
        }
        
        .logout-modal {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            max-width: 450px;
            width: 90%;
            overflow: hidden;
            transform: scale(0.9);
            transition: transform 0.3s ease;
        }
        
        .logout-modal-overlay.show .logout-modal {
            transform: scale(1);
        }
        
        .logout-modal-header {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white;
            padding: 24px 30px;
            text-align: center;
        }
        
        .logout-modal-header i {
            font-size: 3rem;
            margin-bottom: 10px;
            animation: pulseIcon 2s infinite;
        }
        
        @keyframes pulseIcon {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .logout-modal-header h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1.25rem;
        }
        
        .logout-modal-body {
            padding: 30px;
            text-align: center;
        }
        
        .logout-modal-body p {
            color: #4a5568;
            font-size: 1rem;
            margin-bottom: 0;
        }
        
        .logout-modal-footer {
            padding: 20px 30px 30px;
            display: flex;
            gap: 12px;
            justify-content: center;
        }
        
        .logout-modal-footer .btn {
            min-width: 120px;
            font-weight: 600;
            padding: 10px 24px;
        }
        
        .btn-logout-confirm {
            background: linear-gradient(135deg, #1a1a1a 0%, #0d0d0d 100%);
            color: white;
            border: none;
        }
        
        .btn-logout-confirm:hover {
            background: linear-gradient(135deg, #000000 0%, #1a1a1a 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        .btn-logout-cancel {
            background: #e2e8f0;
            color: #4a5568;
            border: none;
        }
        
        .btn-logout-cancel:hover {
            background: #cbd5e0;
            color: #2d3748;
            transform: translateY(-2px);
        }
        
        /* Responsive Styles */
        
        /* Tablet and below */
        @media (max-width: 1024px) {
            .hamburger-btn-navbar {
                display: flex;
            }
            
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .content-wrapper {
                margin-left: 0;
            }
        }
        
        /* Mobile */
        @media (max-width: 768px) {
            .top-navbar {
                padding: 15px 20px;
            }
            
            .container-fluid {
                padding: 0 15px;
            }
            
            .card-header {
                font-size: 0.9rem;
                padding: 12px 15px;
            }
            
            .table-responsive {
                font-size: 0.85rem;
            }
            
            .btn-sm {
                padding: 0.25rem 0.4rem;
                font-size: 0.75rem;
            }
            
            /* Stack stat cards on mobile */
            .col-md-3 {
                margin-bottom: 15px;
            }
        }
        
        /* Small mobile */
        @media (max-width: 480px) {
            .hamburger-btn {
                width: 45px;
                height: 45px;
                top: 10px;
                right: 15px;
            }
            
            .sidebar {
                width: 80%;
            }
            
            .sidebar .logo {
                padding: 15px;
            }
            
            .sidebar .logo i {
                font-size: 2rem;
            }
            
            .sidebar .logo h5 {
                font-size: 1rem;
            }
            
            .sidebar .nav-link {
                padding: 12px 20px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>