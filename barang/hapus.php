<?php
/**
 * ========================================
 * HAPUS BARANG
 * ========================================
 * File: barang/hapus.php
 * Fungsi: Menghapus data barang dengan opsi cascade delete
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';
require_once '../config/log_helper.php';

// Cek parameter id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?status=error&msg=ID tidak ditemukan');
    exit;
}

$id = (int)$_GET['id'];
$force_delete = isset($_GET['force']) && $_GET['force'] === '1';

// Cek apakah barang ada di database
$cek_barang = "SELECT * FROM barang WHERE id = $id LIMIT 1";
$result_barang = mysqli_query($conn, $cek_barang);

if (mysqli_num_rows($result_barang) == 0) {
    header('Location: index.php?status=error&msg=Data barang tidak ditemukan');
    exit;
}

$data_barang = mysqli_fetch_assoc($result_barang);

// Cek apakah barang masih memiliki transaksi
$cek_transaksi_masuk = "SELECT COUNT(*) as total FROM stok_masuk WHERE barang_id = $id";
$result_masuk = mysqli_query($conn, $cek_transaksi_masuk);
$total_masuk = mysqli_fetch_assoc($result_masuk)['total'];

$cek_transaksi_keluar = "SELECT COUNT(*) as total FROM stok_keluar WHERE barang_id = $id";
$result_keluar = mysqli_query($conn, $cek_transaksi_keluar);
$total_keluar = mysqli_fetch_assoc($result_keluar)['total'];

$total_transaksi = $total_masuk + $total_keluar;

// Jika ada transaksi dan user belum konfirmasi force delete
if ($total_transaksi > 0 && !$force_delete) {
    // Redirect kembali dengan parameter untuk menampilkan popup konfirmasi
    $msg = json_encode([
        'id' => $id,
        'nama' => $data_barang['nama_barang'],
        'kode' => $data_barang['kode_barang'],
        'total_transaksi' => $total_transaksi,
        'total_masuk' => $total_masuk,
        'total_keluar' => $total_keluar
    ]);
    header('Location: index.php?status=has_transaction&data=' . urlencode($msg));
    exit;
}

// Mulai transaction
mysqli_begin_transaction($conn);

try {
    // Jika force delete, hapus semua transaksi terkait
    if ($force_delete && $total_transaksi > 0) {
        // Hapus transaksi masuk
        mysqli_query($conn, "DELETE FROM stok_masuk WHERE barang_id = $id");
        
        // Hapus transaksi keluar
        mysqli_query($conn, "DELETE FROM stok_keluar WHERE barang_id = $id");
    }
    
    // Hapus data barang
    mysqli_query($conn, "DELETE FROM barang WHERE id = $id");
    
    // Log activity
    $log_desc = "Menghapus barang: {$data_barang['kode_barang']} - {$data_barang['nama_barang']}";
    if ($force_delete && $total_transaksi > 0) {
        $log_desc .= " (termasuk $total_transaksi riwayat transaksi)";
    }
    
    log_activity(
        $_SESSION['user_id'],
        $_SESSION['username'],
        'DELETE',
        'BARANG',
        $log_desc
    );
    
    // Commit transaction
    mysqli_commit($conn);
    
    header('Location: index.php?status=success_delete');
} catch (Exception $e) {
    // Rollback jika error
    mysqli_rollback($conn);
    header('Location: index.php?status=error&msg=' . urlencode($e->getMessage()));
}

exit;
?>