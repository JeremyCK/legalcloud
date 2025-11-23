-- =====================================================
-- MySQL Script: Office Account Balance Permission
-- =====================================================
-- This script creates/updates the OfficeAccountBalancePermission
-- in the user_access_control table
-- =====================================================

-- =====================================================
-- OPTION 1: INSERT (Create New Record)
-- Use this if the permission doesn't exist yet
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
    'OfficeAccountBalancePermission', -- code (permission identifier)
    1,                              -- status (1 = active, 0 = inactive)
    0,                              -- role_id (0 = not specific to a role)
    0,                              -- branch_id (0 = not specific to a branch)
    '[]',                           -- user_id_list (JSON array of user IDs, empty = all users in role)
    '[]',                           -- branch_id_list (JSON array of branch IDs, empty = all branches)
    '[1,4,5,12]',                   -- role_id_list (JSON array: 1=admin, 4=management, 5=account, 12=maker)
    '[]',                           -- exclusive_branch_list (JSON array, empty = no exclusive branches)
    '[]',                           -- exclude_branch_list (JSON array, empty = no excluded branches)
    NULL,                           -- exclude_user_list (NULL or JSON array, NULL = no excluded users)
    1,                              -- show_in_menu (1 = show in menu, 0 = hide)
    'Office Account Balance',       -- name (display name)
    0,                              -- hierarchy (display order, 0 = default)
    'Account',                      -- type_name (category/group name)
    NOW(),                          -- created_at
    NOW()                           -- updated_at
);

-- =====================================================
-- OPTION 2: UPDATE (Update Existing Record)
-- Use this if the permission already exists
-- =====================================================

UPDATE `user_access_control`
SET
    `status` = 1,
    `role_id_list` = '[1,4,5,12]',      -- Update role IDs if needed
    `user_id_list` = '[]',              -- Update user IDs if needed (empty = use role-based)
    `branch_id_list` = '[]',            -- Update branch IDs if needed (empty = all branches)
    `exclusive_branch_list` = '[]',    -- Update exclusive branches if needed
    `exclude_branch_list` = '[]',      -- Update excluded branches if needed
    `exclude_user_list` = NULL,         -- Update excluded users if needed
    `show_in_menu` = 1,
    `name` = 'Office Account Balance',
    `hierarchy` = 0,
    `type_name` = 'Account',
    `updated_at` = NOW()
WHERE `code` = 'OfficeAccountBalancePermission';

-- =====================================================
-- OPTION 3: INSERT OR UPDATE (MySQL 8.0+)
-- Use this for a single statement that works for both cases
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
    1,
    0,
    'OfficeAccountBalancePermission',
    1,
    0,
    0,
    '[]',
    '[]',
    '[1,4,5,12]',
    '[]',
    '[]',
    NULL,
    1,
    'Office Account Balance',
    0,
    'Account',
    NOW(),
    NOW()
)
ON DUPLICATE KEY UPDATE
    `status` = 1,
    `role_id_list` = '[1,4,5,12]',
    `user_id_list` = '[]',
    `branch_id_list` = '[]',
    `exclusive_branch_list` = '[]',
    `exclude_branch_list` = '[]',
    `exclude_user_list` = NULL,
    `show_in_menu` = 1,
    `name` = 'Office Account Balance',
    `hierarchy` = 0,
    `type_name` = 'Account',
    `updated_at` = NOW();

-- =====================================================
-- VERIFICATION QUERIES
-- =====================================================

-- Check if permission exists
SELECT * FROM `user_access_control` 
WHERE `code` = 'OfficeAccountBalancePermission';

-- View all Account-related permissions
SELECT `id`, `code`, `name`, `status`, `show_in_menu`, `type_name`, `role_id_list`
FROM `user_access_control`
WHERE `type_name` = 'Account'
ORDER BY `hierarchy`, `name`;

-- Check which roles have access (view role names)
SELECT 
    uac.id,
    uac.code,
    uac.name,
    uac.role_id_list,
    GROUP_CONCAT(r.name ORDER BY r.id) as role_names
FROM `user_access_control` uac
LEFT JOIN `roles` r ON JSON_CONTAINS(uac.role_id_list, CAST(r.id AS JSON))
WHERE uac.code = 'OfficeAccountBalancePermission'
GROUP BY uac.id;

-- =====================================================
-- COMMON UPDATES
-- =====================================================

-- Add more roles (e.g., add role ID 6 = lawyer)
-- UPDATE `user_access_control`
-- SET `role_id_list` = '[1,4,5,6,12]'
-- WHERE `code` = 'OfficeAccountBalancePermission';

-- Add specific users (e.g., user IDs 13 and 32)
-- UPDATE `user_access_control`
-- SET `user_id_list` = '[13,32]'
-- WHERE `code` = 'OfficeAccountBalancePermission';

-- Add specific branches (e.g., branch IDs 1, 2, 3)
-- UPDATE `user_access_control`
-- SET `branch_id_list` = '[1,2,3]'
-- WHERE `code` = 'OfficeAccountBalancePermission';

-- Disable the permission (hide from menu and disable access)
-- UPDATE `user_access_control`
-- SET `status` = 0, `show_in_menu` = 0
-- WHERE `code` = 'OfficeAccountBalancePermission';

-- =====================================================
-- NOTES
-- =====================================================
-- Role IDs Reference:
--   1  = admin
--   4  = management
--   5  = account
--   12 = maker
--
-- To find other role IDs:
--   SELECT id, name FROM roles;
--
-- To find user IDs:
--   SELECT id, name FROM users;
--
-- To find branch IDs:
--   SELECT id, name FROM branch;
--
-- JSON Array Format:
--   Empty array: '[]'
--   Single value: '[1]'
--   Multiple values: '[1,4,5,12]'
-- =====================================================


