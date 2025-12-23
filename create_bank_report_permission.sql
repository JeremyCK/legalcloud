-- =====================================================
-- SQL Script: Bank Report Permission
-- =====================================================
-- This script creates/updates the BankReportPermission
-- in the user_access_control table
-- SAFE VERSION: Deletes existing records first, then inserts
-- This prevents duplicates since there's no unique constraint on code
-- =====================================================

-- Step 1: Delete any existing BankReportPermission records
-- This ensures we don't have duplicates
DELETE FROM `user_access_control` WHERE `code` = 'BankReportPermission';

-- Step 2: Insert the new permission record
INSERT INTO `user_access_control` (
    `control_id`, `user_id`, `code`, `status`, `role_id`, `branch_id`,
    `user_id_list`, `branch_id_list`, `role_id_list`,
    `exclusive_branch_list`, `exclude_branch_list`, `exclude_user_list`,
    `show_in_menu`, `name`, `hierarchy`, `type_name`,
    `created_at`, `updated_at`
) VALUES (
    1,                              -- control_id (usually 1)
    0,                              -- user_id (0 = not specific to a user)
    'BankReportPermission',         -- code (permission identifier)
    1,                              -- status (1 = active, 0 = inactive)
    0,                              -- role_id (0 = not specific to a role)
    0,                              -- branch_id (0 = not specific to a branch)
    '[]',                           -- user_id_list (empty = use role-based access)
    '[]',                           -- branch_id_list (empty = all branches)
    '[1,4,5,12]',                   -- role_id_list (1=admin, 4=management, 5=account, 12=maker)
    '[]',                           -- exclusive_branch_list (empty = no exclusive branches)
    '[]',                           -- exclude_branch_list (empty = no excluded branches)
    NULL,                           -- exclude_user_list (NULL = no excluded users)
    1,                              -- show_in_menu (1 = show in menu, 0 = hide)
    'Bank Report',                  -- name (display name)
    0,                              -- hierarchy (display order, 0 = default)
    'Reporting',                    -- type_name (category/group name)
    NOW(),                          -- created_at
    NOW()                           -- updated_at
);

-- =====================================================
-- Verify the permission was created:
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
-- NOTES:
-- =====================================================
-- 1. This script uses DELETE then INSERT to prevent duplicates
--    since there's no unique constraint on the code column
-- 2. control_id: Usually 1, but you may need to check the max value in your table
-- 3. role_id_list: JSON array of role IDs that should have access
--    - 1 = admin
--    - 4 = management
--    - 5 = account
--    - 12 = maker
--    - Adjust these IDs based on your actual role IDs in the roles table
-- 4. user_id_list: Empty array '[]' means use role-based access
--    - To grant to specific users, use: '[1,2,3]' (user IDs)
-- 5. branch_id_list: Empty array '[]' means all branches
--    - To restrict to specific branches, use: '[1,2,3]' (branch IDs)
-- 6. show_in_menu: 1 = show in menu, 0 = hide from menu
-- 7. type_name: 'Reporting' groups it with other reports in the menu
-- =====================================================

