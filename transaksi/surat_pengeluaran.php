<?php
/**
 * ========================================
 * SURAT PENGELUARAN BARANG
 * ========================================
 * File: transaksi/surat_pengeluaran.php
 * Fungsi: Generate surat pengeluaran barang (auto-print)
 * Supports: Single item (id) or Multiple items (batch_id)
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';
require_once '../config/stock_helper.php';

// Determine query type: single item or batch
$is_batch = isset($_GET['batch_id']) && !empty($_GET['batch_id']);

if ($is_batch) {
    // Multiple items - query by batch_id
    $batch_id = escape($_GET['batch_id']);
    
    $query = "SELECT sk.*, b.kode_barang, b.nama_barang, b.kategori, b.satuan 
              FROM stok_keluar sk
              JOIN barang b ON sk.barang_id = b.id
              WHERE sk.batch_id = '$batch_id'
              ORDER BY sk.id ASC";
    
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 0) {
        die('Data transaksi tidak ditemukan!');
    }
    
    // Get first row for common data
    $first_row = mysqli_fetch_assoc($result);
    $penanggung_jawab = $first_row['penanggung_jawab'];
    $tanggal = $first_row['tanggal'];
    
    // Reset pointer to beginning
    mysqli_data_seek($result, 0);
    
} else {
    // Single item - query by id
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        die('ID transaksi tidak valid!');
    }
    
    $transaksi_id = (int)$_GET['id'];
    
    $query = "SELECT sk.*, b.kode_barang, b.nama_barang, b.kategori, b.satuan 
              FROM stok_keluar sk
              JOIN barang b ON sk.barang_id = b.id
              WHERE sk.id = $transaksi_id
              LIMIT 1";
    
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) == 0) {
        die('Data transaksi tidak ditemukan!');
    }
    
    $data = mysqli_fetch_assoc($result);
    $penanggung_jawab = $data['penanggung_jawab'];
    $tanggal = $data['tanggal'];
    
    // Reset for iteration
    mysqli_data_seek($result, 0);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Pengeluaran Barang - <?= $data['kode_barang'] ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 40px;
            background: white;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #000;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #1a1a1a;
            margin-bottom: 5px;
        }
        
        .company-tagline {
            font-size: 12px;
            color: #666;
            letter-spacing: 2px;
        }
        
        /* Title */
        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin: 30px 0;
            text-decoration: underline;
        }
        
        /* Info Section */
        .info-section {
            margin-bottom: 30px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 10px;
        }
        
        .info-label {
            width: 180px;
            font-weight: normal;
        }
        
        .info-value {
            flex: 1;
            font-weight: normal;
        }
        
        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 12px 8px;
            text-align: left;
        }
        
        th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }
        
        td {
            text-align: center;
        }
        
        td.text-left {
            text-align: left;
        }
        
        /* Signature Section */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 60px;
        }
        
        .signature-box {
            text-align: center;
            width: 30%;
        }
        
        .signature-label {
            margin-bottom: 80px;
        }
        
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 10px;
            padding-top: 5px;
        }
        
        /* Print Styles */
        @media print {
            body {
                padding: 20px;
            }
            
            .no-print {
                display: none !important;
            }
            
            @page {
                margin: 1cm;
                size: A4;
            }
        }
        
        /* Print Button */
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #1a1a1a;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 1000;
        }
        
        .print-button:hover {
            background: #2d2d2d;
        }
        
        .print-button i {
            margin-right: 8px;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- html2pdf library for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
</head>
<body>
    <!-- Action Buttons -->
    <button class="print-button no-print" onclick="window.print()" style="top: 20px;">
        <i class="fas fa-print"></i>Print Surat
    </button>
    
    <button class="print-button no-print" onclick="downloadPDF()" style="top: 70px; background: #dc3545;">
        <i class="fas fa-file-pdf"></i>Download PDF
    </button>
    
    <button class="print-button no-print" onclick="window.location.href='stok_keluar.php'" style="top: 120px; background: #6c757d;">
        <i class="fas fa-arrow-left"></i>Kembali
    </button>
    
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="company-name">SISTEM INVENTARIS</div>
            <div class="company-tagline">MANAJEMEN STOK BARANG</div>
        </div>
        
        <!-- Title -->
        <div class="title">SURAT PENGELUARAN BARANG</div>
        
        <!-- Info Section -->
        <div class="info-section">
            <div class="info-row">
                <div class="info-label">Tujuan Pengiriman:</div>
                <div class="info-value"><?= htmlspecialchars($penanggung_jawab) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Tanggal:</div>
                <div class="info-value"><?= tgl_indo($tanggal) ?></div>
            </div>
        </div>
        
        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="20%">Nama Barang</th>
                    <th width="15%">Kode Barang</th>
                    <th width="10%">Jumlah</th>
                    <th width="50%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                while ($row = mysqli_fetch_assoc($result)): 
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td class="text-left"><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td><?= htmlspecialchars($row['kode_barang']) ?></td>
                        <td><strong><?= number_format($row['jumlah']) ?> <?= $row['satuan'] ?></strong></td>
                        <td class="text-left"><?= htmlspecialchars($row['keterangan']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        
        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-label">Dikeluarkan Oleh,</div>
                <div class="signature-line">
                    <?= $_SESSION['nama_lengkap'] ?>
                </div>
            </div>
            
            <div class="signature-box">
                <div class="signature-label">Diterima Oleh,</div>
                <div class="signature-line">
                    <?= htmlspecialchars($penanggung_jawab) ?>
                </div>
            </div>
            
            <div class="signature-box">
                <div class="signature-label">Mengetahui,</div>
                <div class="signature-line">
                    &nbsp;
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-open print dialog when page loads
        window.onload = function() {
            // Small delay to ensure page is fully loaded
            setTimeout(function() {
                window.print();
            }, 500);
        };
        
        // Function to download as PDF using html2pdf
        function downloadPDF() {
            // Get the container element
            const element = document.querySelector('.container');
            
            // PDF options
            const opt = {
                margin: 10,
                filename: 'Surat_Pengeluaran_<?= date('Ymd_His') ?>.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2, useCORS: true },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
            };
            
            // Generate and download PDF
            html2pdf().set(opt).from(element).save();
        }
    </script>
</body>
</html>
