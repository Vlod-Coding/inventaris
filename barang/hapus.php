<?php
/**
 * ========================================
 * HAPUS BARANG
 * ========================================
 * File: barang/hapus.php
 * Fungsi: Menghapus data barang (alternatif dari proses.php)
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';

// Cek parameter id
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php?status=error&msg=ID tidak ditemukan');
    exit;
}

$id = (int)$_GET['id'];

// Cek apakah barang ada di database
$cek_barang = "SELECT nama_barang FROM barang WHERE id = $id LIMIT 1";
$result_barang = mysqli_query($conn, $cek_barang);

if (mysqli_num_rows($result_barang) == 0) {
    // Barang tidak ditemukan
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

if ($total_transaksi > 0) {
    // Barang masih memiliki transaksi, tidak bisa dihapus
    $msg = "Barang '{$data_barang['nama_barang']}' tidak bisa dihapus karena masih memiliki " . 
           $total_transaksi . " riwayat transaksi";
    header('Location: index.php?status=error&msg=' . urlencode($msg));
    exit;
}

// Hapus data barang
$query_hapus = "DELETE FROM barang WHERE id = $id";

if (mysqli_query($conn, $query_hapus)) {
    // Berhasil hapus
    header('Location: index.php?status=success_delete');
} else {
    // Gagal hapus
    $error_msg = mysqli_error($conn);
    header('Location: index.php?status=error&msg=' . urlencode($error_msg));
}

exit;
?>