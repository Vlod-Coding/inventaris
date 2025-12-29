<?php
/**
 * ========================================
 * EXPORT EXCEL - STOK BARANG
 * ========================================
 * File: laporan/export_stok_excel.php
 * Fungsi: Export data stok barang ke format Excel
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';

// Filter kategori (jika ada)
$filter_kategori = isset($_GET['kategori']) ? escape($_GET['kategori']) : '';

// Query untuk mengambil data stok barang
$query = "SELECT * FROM barang WHERE 1=1";

if (!empty($filter_kategori)) {
    $query .= " AND kategori = '$filter_kategori'";
}

$query .= " ORDER BY nama_barang ASC";
$result = mysqli_query($conn, $query);

// Set header untuk download Excel
$filename = "Laporan_Stok_Barang_" . date('Y-m-d_His') . ".xls";
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Mulai output Excel
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
        }
        .header-section {
            margin-bottom: 20px;
        }
        .company-info {
            font-size: 9pt;
            line-height: 1.4;
        }
        .report-title {
            font-size: 16pt;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            color: #2c3e50;
        }
        .info-box {
            border: 2px solid #2c3e50;
            padding: 8px;
            margin: 10px 0;
            background-color: #f8f9fa;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
            width: 150px;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
            font-size: 10pt;
        }
        td {
            font-size: 10pt;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .footer-section {
            margin-top: 20px;
            font-size: 9pt;
            color: #666;
        }
        .total-row {
            background-color: #e8f5e9;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header Section with Merged Cells -->
    <table style="border: none; width: 100%; margin-bottom: 20px;">
        <!-- Company Name -->
        <tr>
            <td colspan="7" style="border: 1px solid #000; text-align: center; padding: 15px; background-color: #f8f9fa;">
                <strong style="font-size: 16pt;">SISTEM INVENTARIS</strong>
            </td>
        </tr>
        <!-- Report Title -->
        <tr>
            <td colspan="7" style="border: 1px solid #000; text-align: center; padding: 12px; background-color: #e9ecef;">
                <strong style="font-size: 14pt;">Laporan Stok Barang</strong>
            </td>
        </tr>
        <!-- Period/Date -->
        <tr>
            <td colspan="7" style="border: 1px solid #000; text-align: center; padding: 10px;">
                <strong>Periode:</strong> <?= date('d F Y') ?>
            </td>
        </tr>
    </table>
    
    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Kode Barang</th>
                <th style="width: 30%;">Nama Barang</th>
                <th style="width: 15%;">Kategori</th>
                <th style="width: 10%;">Satuan</th>
                <th style="width: 10%;">Stok</th>
                <th style="width: 18%;">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (mysqli_num_rows($result) > 0):
                $no = 1;
                $total_stok = 0;
                while ($row = mysqli_fetch_assoc($result)): 
                    $total_stok += $row['stok'];
                    
                    // Tentukan status stok
                    if ($row['stok'] == 0) {
                        $status = 'Habis';
                    } elseif ($row['stok'] < 10) {
                        $status = 'Menipis';
                    } elseif ($row['stok'] < 50) {
                        $status = 'Sedang';
                    } else {
                        $status = 'Aman';
                    }
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $row['kode_barang'] ?></td>
                    <td class="text-left"><?= $row['nama_barang'] ?></td>
                    <td><?= $row['kategori'] ?></td>
                    <td><?= $row['satuan'] ?></td>
                    <td><strong><?= number_format($row['stok']) ?></strong></td>
                    <td><?= $status ?></td>
                </tr>
            <?php 
                endwhile;
            else:
            ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #999;">Tidak ada data</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="5" class="text-right">TOTAL STOK:</th>
                <th><?= isset($total_stok) ? number_format($total_stok) : 0 ?></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
    
    <!-- Footer Section -->
    <div class="footer-section">
        <hr style="border: 1px solid #ddd; margin: 20px 0;">
        <em>Laporan ini digenerate otomatis oleh Sistem Inventaris</em><br>
        <small>Dicetak pada: <?= date('d F Y H:i:s') ?> WIB</small>
    </div>
</body>
</html>
<?php
// Tutup koneksi
mysqli_close($conn);
exit;
?>
