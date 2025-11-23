-- =====================================================
-- SQL Script to Create Office Account Balance Permission
-- =====================================================
-- This script will create the permission record if it doesn't exist,
-- or update it if it already exists.
-- =====================================================

-- First, get the next available control_id
SET @next_control_id = (SELECT COALESCE(MAX(control_id), 0) + 1 FROM user_access_control);

-- Insert or update the permission record
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
    @next_control_id,
    0,                              -- user_id (0 = not specific to a user)
    'OfficeAccountBalancePermission', -- code (permission identifier)
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
    'Office Account Balance',      -- name (display name)
    0,                              -- hierarchy (display order, 0 = default)
    'Account',                      -- type_name (category/group name)
    NOW(),                          -- created_at
    NOW()                           -- updated_at
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
-- Verify the permission was created/updated:
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
WHERE `code` = 'OfficeAccountBalancePermission';

-- =====================================================
-- Notes:
-- =====================================================
-- Role IDs in role_id_list:
--   1  = admin
--   4  = management
--   5  = account
--   12 = maker
--
-- If you need to add more roles, update the role_id_list:
--   UPDATE `user_access_control`
--   SET `role_id_list` = '[1,4,5,12,6]'  -- Add role 6 if needed
--   WHERE `code` = 'OfficeAccountBalancePermission';
--
-- To check which roles exist:
--   SELECT id, name FROM roles ORDER BY id;

