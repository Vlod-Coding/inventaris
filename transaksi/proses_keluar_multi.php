<?php
/**
 * ========================================
 * PROSES STOK KELUAR - MULTIPLE ITEMS
 * ========================================
 * File: transaksi/proses_keluar_multi.php
 * Fungsi: Memproses transaksi stok keluar dengan multiple items
 */

session_start();
require_once '../config/koneksi.php';
require_once '../config/cek_session.php';
require_once '../config/log_helper.php';

// Validasi form sudah disubmit
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: stok_keluar_multi.php');
    exit;
}

// Ambil data common fields
$tanggal = escape($_POST['tanggal']);
$penanggung_jawab = escape($_POST['penanggung_jawab']);
$items = $_POST['items'] ?? [];

// Validasi common fields
if (empty($tanggal) || empty($penanggung_jawab)) {
    header('Location: stok_keluar_multi.php?status=error&msg=Tanggal dan Penanggung Jawab wajib diisi!');
    exit;
}

// Validasi tanggal
if (strtotime($tanggal) > strtotime(date('Y-m-d'))) {
    header('Location: stok_keluar_multi.php?status=error&msg=Tanggal tidak boleh melebihi hari ini');
    exit;
}

// Validasi items
if (empty($items) || count($items) == 0) {
    header('Location: stok_keluar_multi.php?status=error&msg=Minimal harus ada 1 barang!');
    exit;
}

// Generate unique batch_id
$batch_id = 'BATCH-' . date('Ymd-His') . '-' . $_SESSION['user_id'];

// Validasi dan prepare items
$validated_items = [];
$barang_ids = [];

foreach ($items as $item) {
    $barang_id = (int)($item['barang_id'] ?? 0);
    $jumlah = (int)($item['jumlah'] ?? 0);
    $keterangan = escape($item['keterangan'] ?? '');
    
    // Skip empty items
    if ($barang_id <= 0 || $jumlah <= 0) {
        continue;
    }
    
    // Check duplicate
    if (in_array($barang_id, $barang_ids)) {
        header('Location: stok_keluar_multi.php?status=error&msg=Tidak boleh ada barang yang sama dalam satu transaksi!');
        exit;
    }
    
    $barang_ids[] = $barang_id;
    $validated_items[] = [
        'barang_id' => $barang_id,
        'jumlah' => $jumlah,
        'keterangan' => $keterangan
    ];
}

// Final check
if (empty($validated_items)) {
    header('Location: stok_keluar_multi.php?status=error&msg=Tidak ada barang yang valid!');
    exit;
}

// Mulai transaction
mysqli_begin_transaction($conn);

try {
    $total_items = 0;
    $barang_names = [];
    
    foreach ($validated_items as $item) {
        $barang_id = $item['barang_id'];
        $jumlah = $item['jumlah'];
        $keterangan = $item['keterangan'];
        
        // Cek barang dan stok
        $cek_barang = "SELECT id, nama_barang, stok FROM barang WHERE id = $barang_id LIMIT 1";
        $result_barang = mysqli_query($conn, $cek_barang);
        
        if (mysqli_num_rows($result_barang) == 0) {
            throw new Exception("Barang dengan ID $barang_id tidak ditemukan");
        }
        
        $data_barang = mysqli_fetch_assoc($result_barang);
        $stok_tersedia = $data_barang['stok'];
        
        // Validasi stok
        if ($jumlah > $stok_tersedia) {
            throw new Exception("Stok {$data_barang['nama_barang']} tidak mencukupi. Stok tersedia: $stok_tersedia");
        }
        
        // Insert ke stok_keluar
        $query_insert = "INSERT INTO stok_keluar 
                         (batch_id, barang_id, tanggal, jumlah, keterangan, penanggung_jawab) 
                         VALUES 
                         ('$batch_id', $barang_id, '$tanggal', $jumlah, '$keterangan', '$penanggung_jawab')";
        
        if (!mysqli_query($conn, $query_insert)) {
            throw new Exception('Gagal menyimpan transaksi: ' . mysqli_error($conn));
        }
        
        // Update stok barang
        $query_update = "UPDATE barang SET stok = stok - $jumlah WHERE id = $barang_id";
        
        if (!mysqli_query($conn, $query_update)) {
            throw new Exception('Gagal mengupdate stok: ' . mysqli_error($conn));
        }
        
        // Validasi stok tidak negatif
        $cek_stok_akhir = "SELECT stok FROM barang WHERE id = $barang_id";
        $result_stok = mysqli_query($conn, $cek_stok_akhir);
        $stok_akhir = mysqli_fetch_assoc($result_stok)['stok'];
        
        if ($stok_akhir < 0) {
            throw new Exception('Error: Stok ' . $data_barang['nama_barang'] . ' menjadi negatif');
        }
        
        $total_items++;
        $barang_names[] = $data_barang['nama_barang'];
    }
    
    // Commit transaction
    mysqli_commit($conn);
    
    // Log activity
    $barang_list = implode(', ', $barang_names);
    log_activity(
        $_SESSION['user_id'], 
        $_SESSION['username'], 
        'CREATE', 
        'STOK_KELUAR_MULTI', 
        "Input stok keluar (batch): $total_items item - $barang_list"
    );
    
    // Redirect ke surat pengeluaran dengan batch_id
    header('Location: surat_pengeluaran.php?batch_id=' . urlencode($batch_id));
    exit;
    
} catch (Exception $e) {
    // Rollback jika error
    mysqli_rollback($conn);
    
    // Redirect dengan pesan error
    header('Location: stok_keluar_multi.php?status=error&msg=' . urlencode($e->getMessage()));
    exit;
}
?>
