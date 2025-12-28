<?php
/**
 * ========================================
 * EXPORT EXCEL - TRANSAKSI MASUK
 * ========================================
 * File: laporan/export_transaksi_masuk_excel.php
 * Fungsi: Export data transaksi masuk ke format Excel
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';
require_once '../config/stock_helper.php';

// Filter tanggal
$tanggal_awal = isset($_GET['tanggal_awal']) ? escape($_GET['tanggal_awal']) : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? escape($_GET['tanggal_akhir']) : date('Y-m-d');

// Query untuk mengambil data transaksi masuk
$query = "SELECT sm.*, b.kode_barang, b.nama_barang, b.kategori, b.satuan 
          FROM stok_masuk sm
          JOIN barang b ON sm.barang_id = b.id
          WHERE DATE(sm.tanggal) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'
          ORDER BY sm.tanggal DESC, sm.id DESC";

$result = mysqli_query($conn, $query);

// Set header untuk download Excel
$filename = "Laporan_Transaksi_Masuk_" . date('Y-m-d_His') . ".xls";
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
        .info-row {
            border: 1px solid #000;
            padding: 5px;
            margin: 5px 0;
        }
        .info-label {
            font-weight: bold;
            display: inline-block;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-size: 10pt;
        }
        th {
            background-color: #28a745;
            color: white;
            font-weight: bold;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .footer-section {
            margin-top: 10px;
            font-size: 9pt;
            color: #666;
            border: 1px solid #000;
            padding: 5px;
        }
        .total-row {
            background-color: #d4edda;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <table style="border: 1px solid #000; width: 100%; margin-bottom: 10px;">
        <tr>
            <td style="width: 70%; vertical-align: top; padding: 10px;">
                <div class="company-info">
                    <strong style="font-size: 14pt;">SISTEM INVENTARIS</strong><br>
                    Jl. Contoh Alamat No.123<br>
                    Kota, Provinsi 12345<br>
                    Phone: 0123-456789<br>
                    Email: info@inventaris.com
                </div>
            </td>
            <td style="width: 30%; text-align: center; vertical-align: middle; padding: 10px;">
                <strong style="font-size: 12pt;">LAPORAN TRANSAKSI MASUK</strong><br>
                <small>Tanggal Cetak:</small><br>
                <?= date('d/m/Y H:i') ?> WIB
            </td>
        </tr>
    </table>

    <!-- Report Title -->
    <div class="report-title">
        LAPORAN TRANSAKSI STOK MASUK
    </div>

    <!-- Report Info -->
    <div class="info-row">
        <span class="info-label">Dicetak oleh:</span> <?= $_SESSION['nama_lengkap'] ?>
    </div>
    <div class="info-row">
        <span class="info-label">Tanggal Laporan:</span> <?= tgl_indo($tanggal_awal) ?> s/d <?= tgl_indo($tanggal_akhir) ?>
    </div>
    
    <!-- Data Table -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 12%;">Tanggal</th>
                <th style="width: 12%;">Kode Barang</th>
                <th style="width: 20%;">Nama Barang</th>
                <th style="width: 12%;">Kategori</th>
                <th style="width: 10%;">Satuan</th>
                <th style="width: 8%;">Stok</th>
                <th style="width: 15%;">Supplier</th>
                <th style="width: 6%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (mysqli_num_rows($result) > 0):
                $no = 1;
                $total_masuk = 0;
                while ($row = mysqli_fetch_assoc($result)): 
                    $total_masuk += $row['jumlah'];
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= tgl_indo($row['tanggal']) ?></td>
                    <td><?= $row['kode_barang'] ?></td>
                    <td class="text-left"><?= $row['nama_barang'] ?></td>
                    <td><?= $row['kategori'] ?></td>
                    <td><?= $row['satuan'] ?></td>
                    <td><strong><?= number_format($row['jumlah']) ?></strong></td>
                    <td><?= $row['supplier'] ?></td>
                    <td class="text-left"><?= $row['keterangan'] ?></td>
                </tr>
            <?php 
                endwhile;
            else:
            ?>
                <tr>
                    <td colspan="9" style="color: #999;">Tidak ada data</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="6" class="text-right">TOTAL STOK:</th>
                <th><?= isset($total_masuk) ? number_format($total_masuk) : 0 ?></th>
                <th colspan="2"></th>
            </tr>
        </tfoot>
    </table>
    
    <!-- Footer Section -->
    <div class="footer-section">
        <em>Laporan ini digenerate otomatis oleh Sistem Inventaris</em>
    </div>
</body>
</html>
<?php
// Tutup koneksi
mysqli_close($conn);
exit;
?>
