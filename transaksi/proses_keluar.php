<?php
/**
 * ========================================
 * PROSES STOK KELUAR
 * ========================================
 * File: transaksi/proses_keluar.php
 * Fungsi: Memproses transaksi stok keluar dan update stok barang
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';
require_once '../config/log_helper.php';

// Validasi form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: stok_keluar.php');
    exit;
}

// Ambil dan sanitasi data dari form
$tanggal = escape($_POST['tanggal']);
$barang_id = (int)$_POST['barang_id'];
$jumlah = (int)$_POST['jumlah'];
$keterangan = escape($_POST['keterangan']);
$penanggung_jawab = escape($_POST['penanggung_jawab']);

// Validasi data
if (empty($tanggal) || $barang_id <= 0 || $jumlah <= 0 || empty($penanggung_jawab)) {
    header('Location: stok_keluar.php?status=error&msg=Data tidak lengkap. Semua field wajib diisi!');
    exit;
}

// Validasi tanggal tidak boleh lebih dari hari ini
if (strtotime($tanggal) > strtotime(date('Y-m-d'))) {
    header('Location: stok_keluar.php?status=error&msg=Tanggal tidak boleh melebihi hari ini');
    exit;
}

// Cek apakah barang ada di database dan stoknya cukup
$cek_barang = "SELECT id, nama_barang, stok FROM barang WHERE id = $barang_id LIMIT 1";
$result_barang = mysqli_query($conn, $cek_barang);

if (mysqli_num_rows($result_barang) == 0) {
    header('Location: stok_keluar.php?status=error&msg=Barang tidak ditemukan');
    exit;
}

$data_barang = mysqli_fetch_assoc($result_barang);
$stok_tersedia = $data_barang['stok'];

// Validasi stok mencukupi
if ($jumlah > $stok_tersedia) {
    header('Location: stok_keluar.php?status=error&msg=Stok tidak mencukupi. Stok tersedia: ' . $stok_tersedia);
    exit;
}

$stok_baru = $stok_tersedia - $jumlah;

// Mulai transaksi database untuk memastikan data consistency
mysqli_begin_transaction($conn);

try {
    // 1. Insert data ke tabel stok_keluar
    $query_insert = "INSERT INTO stok_keluar 
                     (barang_id, tanggal, jumlah, keterangan, penanggung_jawab) 
                     VALUES 
                     ($barang_id, '$tanggal', $jumlah, '$keterangan', '$penanggung_jawab')";
    
    if (!mysqli_query($conn, $query_insert)) {
        throw new Exception('Gagal menyimpan transaksi: ' . mysqli_error($conn));
    }
    
    // 2. Update stok barang (kurangi stok)
    $query_update = "UPDATE barang 
                     SET stok = stok - $jumlah 
                     WHERE id = $barang_id";
    
    if (!mysqli_query($conn, $query_update)) {
        throw new Exception('Gagal mengupdate stok: ' . mysqli_error($conn));
    }
    
    // Validasi akhir: pastikan stok tidak negatif
    $cek_stok_akhir = "SELECT stok FROM barang WHERE id = $barang_id";
    $result_stok = mysqli_query($conn, $cek_stok_akhir);
    $stok_akhir = mysqli_fetch_assoc($result_stok)['stok'];
    
    if ($stok_akhir < 0) {
        throw new Exception('Error: Stok menjadi negatif. Transaksi dibatalkan.');
    }
    
    // Commit transaksi jika semua berhasil
    mysqli_commit($conn);
    
    // Log activity
    log_activity(
        $_SESSION['user_id'], 
        $_SESSION['username'], 
        'CREATE', 
        'STOK_KELUAR', 
        "Input stok keluar: {$data_barang['nama_barang']} sebanyak $jumlah unit" . ($keterangan ? " ($keterangan)" : "")
    );
    
    // Redirect dengan pesan sukses
    header('Location: stok_keluar.php?status=success');
    exit;
    
} catch (Exception $e) {
    // Rollback jika ada error
    mysqli_rollback($conn);
    
    // Redirect dengan pesan error
    header('Location: stok_keluar.php?status=error&msg=' . urlencode($e->getMessage()));
    exit;
}
?>