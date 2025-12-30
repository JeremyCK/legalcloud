-- =====================================================
-- Bank Report Permission - Deployment Script
-- =====================================================
-- Purpose: Create/Update BankReportPermission in user_access_control table
-- Version: 1.0
-- Date: 2025-12-21
-- =====================================================
-- This script safely creates or updates the Bank Report permission
-- It handles duplicates and includes verification steps
-- =====================================================

-- =====================================================
-- STEP 1: Check for existing records
-- =====================================================
SELECT 
    COUNT(*) as existing_count,
    GROUP_CONCAT(id ORDER BY id) as existing_ids
FROM `user_access_control` 
WHERE `code` = 'BankReportPermission';

-- =====================================================
-- STEP 2: Remove any existing duplicates
-- =====================================================
-- This ensures we only have one record after insertion
DELETE FROM `user_access_control` 
WHERE `code` = 'BankReportPermission';

-- =====================================================
-- STEP 3: Insert the permission record
-- =====================================================
INSERT INTO `user_access_control` (
    `control_id`,
    `user_id`,
    `code`,
    `status`,
    `role_id`,
    `branch_id`,
    `user_id_list`,
    `branch_id_list`,
    `role_id_list`,
    `exclusive_branch_list`,
    `exclude_branch_list`,
    `exclude_user_list`,
    `show_in_menu`,
    `name`,
    `hierarchy`,
    `type_name`,
    `created_at`,
    `updated_at`
) VALUES (
    1,                              -- control_id (usually 1)
    0,                              -- user_id (0 = not specific to a user)
    'BankReportPermission',         -- code (permission identifier - MUST MATCH PermissionController)
    1,                              -- status (1 = active, 0 = inactive)
    0,                              -- role_id (0 = not specific to a role)
    0,                              -- branch_id (0 = not specific to a branch)
    '[]',                           -- user_id_list (empty = use role-based access)
    '[]',                           -- branch_id_list (empty = all branches)
    '[1,4,5,12]',                   -- role_id_list (JSON array: 1=admin, 4=management, 5=account, 12=maker)
    '[]',                           -- exclusive_branch_list (empty = no exclusive branches)
    '[]',                           -- exclude_branch_list (empty = no excluded branches)
    NULL,                           -- exclude_user_list (NULL = no excluded users)
    1,                              -- show_in_menu (1 = show in menu, 0 = hide)
    'Bank Report',                  -- name (display name in menu)
    0,                              -- hierarchy (display order, 0 = default)
    'Reporting',                    -- type_name (category/group name)
    NOW(),                          -- created_at
    NOW()                           -- updated_at
);

-- =====================================================
-- STEP 4: Verify the permission was created successfully
-- =====================================================
SELECT 
    id,
    control_id,
    code,
    name,
    status,
    show_in_menu,
    role_id_list,
    user_id_list,
    branch_id_list,
    type_name,
    created_at,
    updated_at
FROM `user_access_control` 
WHERE `code` = 'BankReportPermission';

-- =====================================================
-- STEP 5: Verify only ONE record exists
-- =====================================================
SELECT 
    COUNT(*) as record_count
FROM `user_access_control` 
WHERE `code` = 'BankReportPermission'
HAVING COUNT(*) != 1;

-- If the above query returns a row, there's a problem (duplicate or missing record)
-- Expected result: No rows returned (meaning exactly 1 record exists)

-- =====================================================
-- CONFIGURATION NOTES:
-- =====================================================
-- Role IDs in role_id_list (adjust based on your roles table):
--   1  = admin
--   4  = management
--   5  = account
--   12 = maker
--
-- To check your actual role IDs:
--   SELECT id, name FROM roles ORDER BY id;
--
-- To update role access after creation:
--   UPDATE `user_access_control`
--   SET `role_id_list` = '[1,4,5,12,6]'  -- Add more role IDs as needed
--   WHERE `code` = 'BankReportPermission';
--
-- To grant access to specific users:
--   UPDATE `user_access_control`
--   SET `user_id_list` = '[10,20,30]'  -- User IDs
--   WHERE `code` = 'BankReportPermission';
--
-- To restrict to specific branches:
--   UPDATE `user_access_control`
--   SET `branch_id_list` = '[1,2,3]'  -- Branch IDs
--   WHERE `code` = 'BankReportPermission';
--
-- To hide from menu (but keep permission active):
--   UPDATE `user_access_control`
--   SET `show_in_menu` = 0
--   WHERE `code` = 'BankReportPermission';
-- =====================================================

-- =====================================================
-- TROUBLESHOOTING:
-- =====================================================
-- If you see duplicate records:
--   1. Run STEP 2 (DELETE) again
--   2. Then run STEP 3 (INSERT) again
--   3. Verify with STEP 4 and STEP 5
--
-- If permission doesn't appear in menu:
--   1. Check that status = 1
--   2. Check that show_in_menu = 1
--   3. Check that user's role is in role_id_list
--   4. Clear browser cache and refresh
--
-- If permission check fails in code:
--   1. Verify code matches exactly: 'BankReportPermission'
--   2. Check PermissionController::BankReportPermission() returns this code
--   3. Verify AccessController::UserAccessPermissionController() is checking correctly
-- =====================================================





