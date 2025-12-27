-- ========================================
-- CREATE ACTIVITY LOGS TABLE
-- ========================================
-- File: config/create_logs_table.sql
-- Fungsi: Membuat tabel untuk menyimpan log aktivitas user

CREATE TABLE IF NOT EXISTS activity_logs (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    username VARCHAR(50) NOT NULL,
    action VARCHAR(50) NOT NULL,
    module VARCHAR(50) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_module (module),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample data untuk testing
INSERT INTO activity_logs (user_id, username, action, module, description, ip_address, user_agent) VALUES
(1, 'admin', 'LOGIN', 'AUTH', 'User berhasil login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(1, 'admin', 'CREATE', 'BARANG', 'Menambah barang: Laptop HP Pavilion', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(1, 'admin', 'UPDATE', 'BARANG', 'Mengubah data barang: Laptop HP Pavilion', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(1, 'admin', 'CREATE', 'STOK_MASUK', 'Input stok masuk: Laptop HP (10 unit)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'),
(1, 'admin', 'CREATE', 'STOK_KELUAR', 'Input stok keluar: Laptop HP (5 unit)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
