-- =====================================================
-- Quick SQL Script: Office Account Balance Permission
-- =====================================================
-- Simple INSERT statement - Run this in your MySQL database
-- =====================================================

INSERT INTO `user_access_control` (
    `control_id`, `user_id`, `code`, `status`, `role_id`, `branch_id`,
    `user_id_list`, `branch_id_list`, `role_id_list`,
    `exclusive_branch_list`, `exclude_branch_list`, `exclude_user_list`,
    `show_in_menu`, `name`, `hierarchy`, `type_name`,
    `created_at`, `updated_at`
) VALUES (
    1, 0, 'OfficeAccountBalancePermission', 1, 0, 0,
    '[]', '[]', '[1,4,5,12]',
    '[]', '[]', NULL,
    1, 'Office Account Balance', 0, 'Account',
    NOW(), NOW()
);

-- =====================================================
-- If permission already exists, use UPDATE instead:
-- =====================================================

UPDATE `user_access_control`
SET
    `status` = 1,
    `role_id_list` = '[1,4,5,12]',
    `show_in_menu` = 1,
    `name` = 'Office Account Balance',
    `type_name` = 'Account',
    `updated_at` = NOW()
WHERE `code` = 'OfficeAccountBalancePermission';

-- =====================================================
-- Verify the permission was created:
-- =====================================================

SELECT * FROM `user_access_control` 
WHERE `code` = 'OfficeAccountBalancePermission';


