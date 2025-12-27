-- ========================================
-- ADD USER ROLES TO USERS TABLE
-- ========================================
-- File: config/add_user_roles.sql
-- Purpose: Add role-based access control to the inventory system
-- Roles: owner, cs (customer service), administrator

-- Add role column to users table
ALTER TABLE `users` 
ADD COLUMN `role` ENUM('owner', 'cs', 'administrator') NOT NULL DEFAULT 'cs' AFTER `password`;

-- Update existing user to administrator role
-- Assuming the first user (id=1) should be administrator
UPDATE `users` 
SET `role` = 'administrator' 
WHERE `id` = 1 
LIMIT 1;

-- Add index for better query performance
ALTER TABLE `users` 
ADD INDEX `idx_role` (`role`);

-- Display results
SELECT 'Migration completed successfully!' as status;
SELECT id, username, nama_lengkap, role FROM users;
