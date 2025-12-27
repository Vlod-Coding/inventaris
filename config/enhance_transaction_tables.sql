-- ========================================
-- ENHANCE TRANSACTION TABLES
-- ========================================
-- File: config/enhance_transaction_tables.sql
-- Fungsi: Menambah kolom baru untuk stok masuk dan keluar

-- 1. Tambah kolom keterangan di stok_masuk
ALTER TABLE stok_masuk 
ADD COLUMN keterangan TEXT NULL AFTER supplier;

-- 2. Tambah kolom penanggung_jawab di stok_keluar
ALTER TABLE stok_keluar 
ADD COLUMN penanggung_jawab VARCHAR(100) NOT NULL DEFAULT '' AFTER keterangan;

-- Catatan: 
-- - keterangan di stok_masuk: opsional, untuk catatan tambahan
-- - penanggung_jawab di stok_keluar: wajib diisi, untuk tracking siapa yang ambil barang
