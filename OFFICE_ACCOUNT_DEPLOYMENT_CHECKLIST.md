# Office Account Balance - Deployment Checklist

## Overview
This document lists all changes required to deploy the Office Account Balance feature.

---

## 1. Database Changes

### Required SQL Script
**File:** `create_office_permission.sql`

Run this SQL script in your production database to create the permission record:

```sql
SET @next_control_id = (SELECT COALESCE(MAX(control_id), 0) + 1 FROM user_access_control);

INSERT INTO `user_access_control` (
    `control_id`, `user_id`, `code`, `status`, `role_id`, `branch_id`,
    `user_id_list`, `branch_id_list`, `role_id_list`,
    `exclusive_branch_list`, `exclude_branch_list`, `exclude_user_list`,
    `show_in_menu`, `name`, `hierarchy`, `type_name`,
    `created_at`, `updated_at`
) VALUES (
    @next_control_id, 0, 'OfficeAccountBalancePermission', 1, 0, 0,
    '[]', '[]', '[1,4,5,12]',
    '[]', '[]', NULL,
    1, 'Office Account Balance', 0, 'Account',
    NOW(), NOW()
)
ON DUPLICATE KEY UPDATE
    `status` = 1,
    `role_id_list` = '[1,4,5,12]',
    `show_in_menu` = 1,
    `name` = 'Office Account Balance',
    `type_name` = 'Account',
    `updated_at` = NOW();
```

**Note:** Role IDs `[1,4,5,12]` represent: admin, management, account, maker. Adjust if needed.

---

## 2. Code Changes

### A. Controller Files

#### `app/Http/Controllers/AccountController.php`
**Changes:**
- Added `officeAccountLedger()` method (line ~2506)
- Added `getOfficeAccountLedger(Request $request)` method
- Added `exportOfficeAccountLedger(Request $request)` method
- Added `exportOfficeAccountLedgerToExcel($data, $year, $month, $branch_id)` private method
- Added `getOfficeBankAccount()` private method

**Key Methods:**
```php
public function officeAccountLedger()
public function getOfficeAccountLedger(Request $request)
public function exportOfficeAccountLedger(Request $request)
private function exportOfficeAccountLedgerToExcel($data, $year, $month, $branch_id)
private function getOfficeBankAccount()
```

#### `app/Http/Controllers/PermissionController.php`
**Changes:**
- Added `OfficeAccountBalancePermission()` static method

**Method:**
```php
public static function OfficeAccountBalancePermission()
{
    return 'OfficeAccountBalancePermission';
}
```

#### `app/Http/Controllers/AccessController.php`
**Changes:**
- Fixed `UserAccessPermissionController()` method to properly check `role_id_list` when `exclusive_branch_list` and `exclude_branch_list` are empty

**Fix Applied:**
- Changed condition from `if ($UserAccessControl->exclusive_branch_list != '')` 
- To: `if ($UserAccessControl->exclusive_branch_list != '' && $UserAccessControl->exclusive_branch_list != '[]')`
- Same fix for `exclude_branch_list`

---

### B. View Files

#### `resources/views/dashboard/account/office-account-ledger.blade.php`
**Status:** New file created
**Purpose:** Main view for Office Account Ledger page

#### `resources/views/dashboard/account/table/tab-office-account-ledger.blade.php`
**Status:** New file created
**Purpose:** Table partial for displaying office account balances

---

### C. Routes

#### `routes/web.php`
**Changes:** Added routes within the authenticated middleware group:

```php
Route::get('office-account-ledger', [AccountController::class, 'officeAccountLedger']);
Route::post('getOfficeAccountLedger', [AccountController::class, 'getOfficeAccountLedger']);
Route::post('exportOfficeAccountLedger', [AccountController::class, 'exportOfficeAccountLedger']);
```

**Location:** Should be within the `Route::middleware(['auth', 'role:admin,account,management,lawyer,maker'])->group()` block

---

### D. Navigation Menu

#### `resources/views/dashboard/shared/nav-builder-v2.blade.php`
**Changes:**
- Added permission check to show "Account" dropdown (line ~90)
- Added menu item for "Office Account Balance" (line ~128-132)

**Code Added:**
```php
@if (AccessController::UserAccessPermissionController(PermissionController::OfficeAccountBalancePermission()) == true)
    <li class="c-sidebar-nav-item">
        <a class="c-sidebar-nav-link" href="{{ url('office-account-ledger') }}">
            <span class="c-sidebar-nav-icon"></span>Office Account Balance
            <div id="side_87"></div>
        </a>
    </li>
@endif
```

---

## 3. Dependencies

### Required PHP Packages
- `PhpOffice\PhpSpreadsheet` (for Excel export)
  - Already included if client ledger export works

### Database Tables Used
- `office_bank_account` (existing table)
- `ledger_entries_v2` (existing table)
- `user_access_control` (existing table - needs permission record)

---

## 4. Deployment Steps

### Step 1: Backup Database
```bash
# Backup user_access_control table
mysqldump -u [username] -p [database_name] user_access_control > backup_user_access_control.sql
```

### Step 2: Deploy Code Changes
1. Upload all modified files to production server
2. Ensure file permissions are correct
3. Verify all new view files are in place

### Step 3: Run Database Migration
1. Connect to production database
2. Run the SQL script from `create_office_permission.sql`
3. Verify the permission record was created:
   ```sql
   SELECT * FROM user_access_control WHERE code = 'OfficeAccountBalancePermission';
   ```

### Step 4: Clear Caches
```bash
php artisan cache:clear
php artisan route:clear
php artisan config:clear
php artisan view:clear
```

### Step 5: Test Access
1. Log in as admin@admin.com (or user with role ID 1, 4, 5, or 12)
2. Verify "Office Account Balance" appears in sidebar under "Account" dropdown
3. Click the menu item and verify the page loads
4. Test filtering by year, month, branch
5. Test Excel export functionality

---

## 5. Verification Checklist

- [ ] Permission record exists in `user_access_control` table
- [ ] Permission has `status = 1` and `show_in_menu = 1`
- [ ] Permission `role_id_list` includes admin role (ID: 1)
- [ ] All code files uploaded to production
- [ ] All view files uploaded to production
- [ ] Routes are accessible (check `php artisan route:list | grep office-account`)
- [ ] Caches cleared
- [ ] Menu item appears in sidebar for admin user
- [ ] Page loads without errors
- [ ] Data displays correctly
- [ ] Excel export works

---

## 6. Rollback Plan

If issues occur, you can rollback by:

1. **Remove permission record:**
   ```sql
   DELETE FROM user_access_control WHERE code = 'OfficeAccountBalancePermission';
   ```

2. **Revert code changes:**
   - Remove routes from `web.php`
   - Remove menu item from `nav-builder-v2.blade.php`
   - Remove methods from `AccountController.php`
   - Remove method from `PermissionController.php`
   - Delete view files

3. **Clear caches:**
   ```bash
   php artisan cache:clear
   php artisan route:clear
   ```

---

## 7. Files Summary

### New Files Created:
- `resources/views/dashboard/account/office-account-ledger.blade.php`
- `resources/views/dashboard/account/table/tab-office-account-ledger.blade.php`
- `create_office_permission.sql`
- `office_account_permission.sql` (alternative script)
- `office_account_permission_simple.sql` (simplified script)

### Files Modified:
- `app/Http/Controllers/AccountController.php`
- `app/Http/Controllers/PermissionController.php`
- `app/Http/Controllers/AccessController.php` (bug fix)
- `routes/web.php`
- `resources/views/dashboard/shared/nav-builder-v2.blade.php`

---

## 8. Important Notes

1. **Role IDs:** The permission is configured for roles: 1 (admin), 4 (management), 5 (account), 12 (maker). If your production environment has different role IDs, update the SQL script accordingly.

2. **AccessController Fix:** The fix to `AccessController.php` is critical - without it, the permission check will fail even if the permission record exists.

3. **Cache Clearing:** Always clear all caches after deployment, especially route and view caches.

4. **Testing:** Test with a non-admin user first if possible, then with admin to ensure permission system works correctly.

---

## 9. Support

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Verify permission record exists and is active
3. Check user's role ID matches the `role_id_list` in permission
4. Verify routes are registered: `php artisan route:list`
5. Check browser console for JavaScript errors

---

**Last Updated:** Based on implementation completed for Office Account Balance feature

