-- SQL Script to add Audit Trail menu item and permissions
-- Run this script on existing databases to add Audit Trail to the menu

-- Step 1: Find the Logs dropdown menu ID
SET @logs_dropdown_id = (
    SELECT id FROM menus 
    WHERE slug = 'dropdown' 
    AND menu_id = (SELECT id FROM menulist WHERE name = 'sidebar menu' LIMIT 1)
    AND id IN (
        SELECT menus_id FROM menus_lang WHERE name = 'Logs' LIMIT 1
    )
    LIMIT 1
);

-- Step 2: Get the last sequence number for items under Logs dropdown
SET @last_sequence = (
    SELECT COALESCE(MAX(sequence), 0) FROM menus 
    WHERE parent_id = @logs_dropdown_id
);

-- Step 3: Insert the Audit Trail menu item
INSERT INTO menus (slug, icon, href, menu_id, parent_id, sequence)
VALUES (
    'link',
    'cil-history',
    '/audit-trail',
    (SELECT id FROM menulist WHERE name = 'sidebar menu' LIMIT 1),
    @logs_dropdown_id,
    @last_sequence + 1
);

SET @menu_id = LAST_INSERT_ID();

-- Step 4: Add menu translations
INSERT INTO menus_lang (name, lang, menus_id)
VALUES 
    ('Audit Trail', 'en', @menu_id);

-- Step 5: Add menu roles (admin and management)
INSERT INTO menu_role (role_name, menus_id)
VALUES 
    ('admin', @menu_id),
    ('management', @menu_id);

-- Step 6: Create permission using Spatie Permission
-- Note: This assumes Spatie Permission package is installed
-- The permission name follows the pattern: "visit Audit Trail"
INSERT INTO permissions (name, guard_name, created_at, updated_at)
SELECT 'visit Audit Trail', 'web', NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM permissions WHERE name = 'visit Audit Trail' AND guard_name = 'web'
);

-- Step 7: Assign permission to admin and management roles
-- Get role IDs (adjust these if your role IDs are different)
SET @admin_role_id = (SELECT id FROM roles WHERE name = 'admin' LIMIT 1);
SET @management_role_id = (SELECT id FROM roles WHERE name = 'management' LIMIT 1);
SET @permission_id = (SELECT id FROM permissions WHERE name = 'visit Audit Trail' AND guard_name = 'web' LIMIT 1);

-- Assign permission to admin role
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT @permission_id, @admin_role_id
WHERE NOT EXISTS (
    SELECT 1 FROM role_has_permissions 
    WHERE permission_id = @permission_id AND role_id = @admin_role_id
);

-- Assign permission to management role
INSERT INTO role_has_permissions (permission_id, role_id)
SELECT @permission_id, @management_role_id
WHERE NOT EXISTS (
    SELECT 1 FROM role_has_permissions 
    WHERE permission_id = @permission_id AND role_id = @management_role_id
);

-- Verification queries (uncomment to verify)
-- SELECT * FROM menus WHERE id = @menu_id;
-- SELECT * FROM menus_lang WHERE menus_id = @menu_id;
-- SELECT * FROM menu_role WHERE menus_id = @menu_id;
-- SELECT * FROM permissions WHERE name = 'visit Audit Trail';
-- SELECT * FROM role_has_permissions WHERE permission_id = @permission_id;




