-- ========================================
-- MIGRATION: Add batch_id to stok_keluar
-- ========================================
-- Purpose: Enable grouping multiple items in one transaction
-- Date: 2025-12-29

-- Add batch_id column
ALTER TABLE stok_keluar 
ADD COLUMN batch_id VARCHAR(50) NULL AFTER id;

-- Add index for better query performance
ALTER TABLE stok_keluar 
ADD INDEX idx_batch_id (batch_id);

-- Verify changes
DESCRIBE stok_keluar;
