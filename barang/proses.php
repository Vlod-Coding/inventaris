<?php
/**
 * ========================================
 * PROSES CRUD BARANG
 * ========================================
 * File: barang/proses.php
 * Fungsi: Memproses tambah, edit, dan hapus data barang
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';

// Cek parameter aksi
if (!isset($_GET['aksi'])) {
    header('Location: index.php');
    exit;
}

$aksi = $_GET['aksi'];

/**
 * ========================================
 * PROSES TAMBAH BARANG
 * ========================================
 */
if ($aksi == 'tambah') {
    
    // Validasi form sudah disubmit
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        header('Location: tambah.php');
        exit;
    }
    
    // Ambil dan sanitasi data dari form
    $kode_barang = escape($_POST['kode_barang']);
    $nama_barang = escape($_POST['nama_barang']);
    $kategori = escape($_POST['kategori']);
    $satuan = escape($_POST['satuan']);
    $stok = (int)$_POST['stok'];
    
    // Validasi kode barang tidak boleh duplikat
    $cek_kode = "SELECT id FROM barang WHERE kode_barang = '$kode_barang'";
    $result_cek = mysqli_query($conn, $cek_kode);
    
    if (mysqli_num_rows($result_cek) > 0) {
        // Kode barang sudah ada
        header('Location: tambah.php?status=error&msg=Kode barang sudah digunakan');
        exit;
    }
    
    // Query insert data barang
    $query = "INSERT INTO barang 
              (kode_barang, nama_barang, kategori, satuan, stok) 
              VALUES 
              ('$kode_barang', '$nama_barang', '$kategori', '$satuan', $stok)";
    
    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        // Berhasil tambah data
        header('Location: index.php?status=success_add');
    } else {
        // Gagal tambah data
        $error_msg = mysqli_error($conn);
        header('Location: tambah.php?status=error&msg=' . urlencode($error_msg));
    }
    exit;
}

/**
 * ========================================
 * PROSES EDIT BARANG
 * ========================================
 */
elseif ($aksi == 'edit') {
    
    // Validasi form sudah disubmit
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        header('Location: index.php');
        exit;
    }
    
    // Ambil dan sanitasi data dari form
    $id = (int)$_POST['id'];
    $kode_barang = escape($_POST['kode_barang']);
    $nama_barang = escape($_POST['nama_barang']);
    $kategori = escape($_POST['kategori']);
    $satuan = escape($_POST['satuan']);
    
    // Validasi kode barang tidak boleh duplikat (kecuali dengan dirinya sendiri)
    $cek_kode = "SELECT id FROM barang 
                 WHERE kode_barang = '$kode_barang' 
                 AND id != $id";
    $result_cek = mysqli_query($conn, $cek_kode);
    
    if (mysqli_num_rows($result_cek) > 0) {
        // Kode barang sudah digunakan oleh data lain
        header('Location: edit.php?id=' . $id . '&status=error&msg=Kode barang sudah digunakan');
        exit;
    }
    
    // Query update data barang (stok tidak diupdate di sini)
    $query = "UPDATE barang SET 
              kode_barang = '$kode_barang',
              nama_barang = '$nama_barang',
              kategori = '$kategori',
              satuan = '$satuan'
              WHERE id = $id";
    
    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        // Berhasil update data
        header('Location: index.php?status=success_edit');
    } else {
        // Gagal update data
        $error_msg = mysqli_error($conn);
        header('Location: edit.php?id=' . $id . '&status=error&msg=' . urlencode($error_msg));
    }
    exit;
}

/**
 * ========================================
 * PROSES HAPUS BARANG
 * ========================================
 */
elseif ($aksi == 'hapus') {
    
    // Cek parameter id
    if (!isset($_GET['id'])) {
        header('Location: index.php');
        exit;
    }
    
    $id = (int)$_GET['id'];
    
    // Cek apakah barang masih memiliki transaksi
    $cek_transaksi = "SELECT 
                      (SELECT COUNT(*) FROM stok_masuk WHERE barang_id = $id) +
                      (SELECT COUNT(*) FROM stok_keluar WHERE barang_id = $id) as total_transaksi";
    $result_transaksi = mysqli_query($conn, $cek_transaksi);
    $total_transaksi = mysqli_fetch_assoc($result_transaksi)['total_transaksi'];
    
    if ($total_transaksi > 0) {
        // Barang masih memiliki transaksi, tidak bisa dihapus
        header('Location: index.php?status=error&msg=Barang tidak bisa dihapus karena masih memiliki riwayat transaksi');
        exit;
    }
    
    // Query hapus data barang
    $query = "DELETE FROM barang WHERE id = $id";
    
    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        // Berhasil hapus data
        header('Location: index.php?status=success_delete');
    } else {
        // Gagal hapus data
        $error_msg = mysqli_error($conn);
        header('Location: index.php?status=error&msg=' . urlencode($error_msg));
    }
    exit;
}

/**
 * ========================================
 * AKSI TIDAK DIKENALI
 * ========================================
 */
else {
    header('Location: index.php');
    exit;
}
?>