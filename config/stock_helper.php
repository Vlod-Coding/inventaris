<?php
/**
 * ========================================
 * STOCK STATUS HELPER
 * ========================================
 * File: config/stock_helper.php
 * Fungsi: Helper function untuk status stok yang konsisten
 */

/**
 * Get stock status dengan kriteria standar
 * 
 * Status Criteria:
 * - Aman: stok >= 10
 * - Menipis: 1 <= stok < 10
 * - Habis: stok = 0
 * 
 * @param int $stok - Jumlah stok barang
 * @return array - Array dengan key: status, class, icon, color
 */
function get_stock_status($stok) {
    $stok = (int)$stok;
    
    if ($stok >= 10) {
        return [
            'status' => 'Aman',
            'class' => 'success',
            'badge_class' => 'bg-success',
            'icon' => 'check-circle',
            'color' => '#28a745'
        ];
    } elseif ($stok > 0) {
        return [
            'status' => 'Menipis',
            'class' => 'warning',
            'badge_class' => 'bg-warning text-dark',
            'icon' => 'exclamation-triangle',
            'color' => '#ffc107'
        ];
    } else {
        return [
            'status' => 'Habis',
            'class' => 'danger',
            'badge_class' => 'bg-danger',
            'icon' => 'times-circle',
            'color' => '#dc3545'
        ];
    }
}

/**
 * Render stock status badge HTML
 * 
 * @param int $stok - Jumlah stok barang
 * @return string - HTML badge
 */
function render_stock_badge($stok) {
    $status = get_stock_status($stok);
    
    return sprintf(
        '<span class="badge %s"><i class="fas fa-%s me-1"></i>%s</span>',
        $status['badge_class'],
        $status['icon'],
        $status['status']
    );
}

/**
 * Get stock statistics
 * Sesuai dengan kriteria status standar
 * 
 * @param mysqli $conn - Database connection
 * @return array - Array dengan statistik stok
 */
function get_stock_statistics($conn) {
    $stats = [
        'total_items' => 0,
        'total_stock' => 0,
        'stock_safe' => 0,    // >= 10
        'stock_low' => 0       // < 10
    ];
    
    $query = "SELECT 
                COUNT(*) as total_items,
                SUM(stok) as total_stock,
                SUM(CASE WHEN stok >= 10 THEN 1 ELSE 0 END) as stock_safe,
                SUM(CASE WHEN stok < 10 THEN 1 ELSE 0 END) as stock_low
              FROM barang";
    
    $result = mysqli_query($conn, $query);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $stats = $row;
    }
    
    return $stats;
}

/**
 * Konstanta threshold untuk referensi
 */
define('STOCK_THRESHOLD_SAFE', 10);
define('STOCK_THRESHOLD_LOW', 1);
?>
