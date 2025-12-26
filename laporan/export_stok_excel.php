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
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <h2>LAPORAN STOK BARANG</h2>
    <p>Tanggal Cetak: <?= date('d F Y H:i:s') ?> WIB</p>
    <?php if (!empty($filter_kategori)): ?>
        <p>Kategori: <?= $filter_kategori ?></p>
    <?php endif; ?>
    <p>Dicetak oleh: <?= $_SESSION['nama_lengkap'] ?></p>
    <br>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Kode Barang</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Satuan</th>
                <th class="text-center">Stok</th>
                <th>Status</th>
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
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= $row['kode_barang'] ?></td>
                    <td><?= $row['nama_barang'] ?></td>
                    <td><?= $row['kategori'] ?></td>
                    <td><?= $row['satuan'] ?></td>
                    <td class="text-center"><?= number_format($row['stok']) ?></td>
                    <td><?= $status ?></td>
                </tr>
            <?php 
                endwhile;
            else:
            ?>
                <tr>
                    <td colspan="7" class="text-center">Tidak ada data</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="5" class="text-right">TOTAL STOK:</th>
                <th class="text-center"><?= isset($total_stok) ? number_format($total_stok) : 0 ?></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
    
    <br>
    <p><em>Laporan ini digenerate otomatis oleh Sistem Inventaris</em></p>
</body>
</html>
<?php
// Tutup koneksi
mysqli_close($conn);
exit;
?>