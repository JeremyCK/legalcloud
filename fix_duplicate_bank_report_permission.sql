-- =====================================================
-- Fix Duplicate Bank Report Permission
-- =====================================================
-- This script removes duplicate BankReportPermission records
-- Keep only the first one (lowest ID)
-- =====================================================

-- First, check how many duplicates exist
SELECT COUNT(*) as duplicate_count, code 
FROM `user_access_control` 
WHERE `code` = 'BankReportPermission'
GROUP BY code;

-- Delete duplicates, keeping only the record with the lowest ID
DELETE t1 FROM `user_access_control` t1
INNER JOIN `user_access_control` t2 
WHERE t1.id > t2.id 
AND t1.code = 'BankReportPermission' 
AND t2.code = 'BankReportPermission';

-- Verify only one record remains
SELECT * FROM `user_access_control` 
WHERE `code` = 'BankReportPermission';

-- =====================================================
-- Alternative: Delete all and re-insert (if you prefer)
-- =====================================================
-- DELETE FROM `user_access_control` WHERE `code` = 'BankReportPermission';
-- 
-- INSERT INTO `user_access_control` (
--     `control_id`, `user_id`, `code`, `status`, `role_id`, `branch_id`,
--     `user_id_list`, `branch_id_list`, `role_id_list`,
--     `exclusive_branch_list`, `exclude_branch_list`, `exclude_user_list`,
--     `show_in_menu`, `name`, `hierarchy`, `type_name`,
--     `created_at`, `updated_at`
-- ) VALUES (
--     1, 0, 'BankReportPermission', 1, 0, 0,
--     '[]', '[]', '[1,4,5,12]',
--     '[]', '[]', NULL,
--     1, 'Bank Report', 0, 'Reporting',
--     NOW(), NOW()
-- );
-- =====================================================



