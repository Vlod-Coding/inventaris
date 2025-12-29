<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title . ' - ' : '' ?>Sistem Inventaris</title>
    
    <?php
    // Deteksi asset path berdasarkan lokasi file
    // Jika file di root (index.php), gunakan 'assets/'
    // Jika file di subfolder (barang/index.php), gunakan '../assets/'
    $asset_path = (basename(dirname($_SERVER['SCRIPT_FILENAME'])) === 'inventaris') ? 'assets' : '../assets';
    ?>
    
    <!-- Bootstrap CSS (Local) -->
    <link href="<?= $asset_path ?>/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome (Local) -->
    <link rel="stylesheet" href="<?= $asset_path ?>/css/fontawesome.min.css">
    
    <!-- DataTables CSS (Local) -->
    <link rel="stylesheet" href="<?= $asset_path ?>/css/dataTables.bootstrap5.min.css">
    
    <!-- SweetAlert2 CSS (Local) -->
    <link rel="stylesheet" href="<?= $asset_path ?>/css/sweetalert2.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $asset_path ?>/css/custom.css">
</head>
<body>
    <!-- Sidebar Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>