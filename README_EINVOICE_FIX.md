# E-Invoice Client Profile Status Fix

## Overview
This fix updates the `client_profile_completed` status for E-Invoice records to match what the detail page displays.

## Problem
- The detail page shows the invoice's billing party status
- But `einvoice_details.client_profile_completed` was based on all billing parties for the bill
- This caused a mismatch where detail page showed "Completed" but main list showed "Pending"

## Solution
Updated the logic to mark a detail as "Completed" if:
1. The invoice's billing party is completed (matches detail page), OR
2. All billing parties for the bill are completed, OR
3. No billing parties exist (default to completed)

## Files Provided

### 1. `fix_einvoice_client_profile_status.sql`
**MySQL script** - Can be run directly on the server database.

**Usage:**
```bash
mysql -u username -p database_name < fix_einvoice_client_profile_status.sql
```

Or via phpMyAdmin/MySQL Workbench:
1. Open the SQL file
2. Execute it on your database

### 2. `fix_einvoice_client_profile_status_php.php`
**PHP script** - Uses Laravel models, can be run via command line.

**Usage:**
```bash
cd /path/to/your/laravel/project
php fix_einvoice_client_profile_status_php.php
```

## What the Scripts Do

### Step 1: Update `einvoice_details.client_profile_completed`
- Checks if invoice's billing party is completed
- If not, checks if all billing parties for the bill are completed
- Updates the detail status accordingly

### Step 2: Update `einvoice_main.client_profile_completed`
- Checks if ALL details for the main record are completed
- Updates main status to "Completed" only if all details are completed

## Verification

After running the script, you can verify the results:

```sql
-- Check details status
SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN client_profile_completed = 1 THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN client_profile_completed = 0 THEN 1 ELSE 0 END) as pending
FROM einvoice_details
WHERE status <> 99;

-- Check main status
SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN client_profile_completed = 1 THEN 1 ELSE 0 END) as completed,
    SUM(CASE WHEN client_profile_completed = 0 THEN 1 ELSE 0 END) as pending
FROM einvoice_main
WHERE status <> 99;
```

## Recommendation

**Use the SQL script** (`fix_einvoice_client_profile_status.sql`) for server deployment as it:
- Is faster (single query vs multiple model operations)
- Doesn't require Laravel to be loaded
- Can be run directly on the database
- Is easier to review and verify

## Backup

**IMPORTANT:** Always backup your database before running any update scripts!

```bash
mysqldump -u username -p database_name > backup_before_einvoice_fix.sql
```

## Notes

- The script only updates records where `status <> 99` (not deleted)
- The fix is idempotent - safe to run multiple times
- No data is deleted, only status flags are updated
