<?php
/**
 * ========================================
 * PROSES STOK MASUK
 * ========================================
 * File: transaksi/proses_masuk.php
 * Fungsi: Memproses transaksi stok masuk dan update stok barang
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';

// Validasi form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: stok_masuk.php');
    exit;
}

// Ambil dan sanitasi data dari form
$tanggal = escape($_POST['tanggal']);
$barang_id = (int)$_POST['barang_id'];
$jumlah = (int)$_POST['jumlah'];
$supplier = escape($_POST['supplier']);

// Validasi data
if (empty($tanggal) || $barang_id <= 0 || $jumlah <= 0) {
    header('Location: stok_masuk.php?status=error&msg=Data tidak lengkap atau tidak valid');
    exit;
}

// Validasi tanggal tidak boleh lebih dari hari ini
if (strtotime($tanggal) > strtotime(date('Y-m-d'))) {
    header('Location: stok_masuk.php?status=error&msg=Tanggal tidak boleh melebihi hari ini');
    exit;
}

// Cek apakah barang ada di database
$cek_barang = "SELECT id, nama_barang, stok FROM barang WHERE id = $barang_id LIMIT 1";
$result_barang = mysqli_query($conn, $cek_barang);

if (mysqli_num_rows($result_barang) == 0) {
    header('Location: stok_masuk.php?status=error&msg=Barang tidak ditemukan');
    exit;
}

$data_barang = mysqli_fetch_assoc($result_barang);
$stok_lama = $data_barang['stok'];
$stok_baru = $stok_lama + $jumlah;

// Mulai transaksi database untuk memastikan data consistency
mysqli_begin_transaction($conn);

try {
    // 1. Insert data ke tabel stok_masuk
    $query_insert = "INSERT INTO stok_masuk 
                     (barang_id, tanggal, jumlah, supplier) 
                     VALUES 
                     ($barang_id, '$tanggal', $jumlah, '$supplier')";
    
    if (!mysqli_query($conn, $query_insert)) {
        throw new Exception('Gagal menyimpan transaksi: ' . mysqli_error($conn));
    }
    
    // 2. Update stok barang (tambah stok)
    $query_update = "UPDATE barang 
                     SET stok = stok + $jumlah 
                     WHERE id = $barang_id";
    
    if (!mysqli_query($conn, $query_update)) {
        throw new Exception('Gagal mengupdate stok: ' . mysqli_error($conn));
    }
    
    // Commit transaksi jika semua berhasil
    mysqli_commit($conn);
    
    // Redirect dengan pesan sukses
    header('Location: stok_masuk.php?status=success');
    exit;
    
} catch (Exception $e) {
    // Rollback jika ada error
    mysqli_rollback($conn);
    
    // Redirect dengan pesan error
    header('Location: stok_masuk.php?status=error&msg=' . urlencode($e->getMessage()));
    exit;
}
?>