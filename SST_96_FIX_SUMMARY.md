# SST Record 96 Fix Summary

## Problem
SST Record 96 shows all invoices with **0.00** for SST, Reimb SST, and Total SST columns, even though invoices have been added to the SST record.

## Root Cause
The `sst_details.amount` field in the database is **0 or NULL** for all records in SST 96. This field should contain the SST amount from the invoice's `sst_inv` field.

## Solution
Update the `sst_details.amount` field to match the invoice's `sst_inv` field, then recalculate the `sst_main.amount` to include both SST and reimbursement SST.

## Files Created

### 1. `diagnose_sst_96_issue.sql`
**Purpose**: Diagnostic query to check what data exists and what's missing

**Usage**: Run this first to see the current state of the data
```sql
-- Run in MySQL/phpMyAdmin
SOURCE diagnose_sst_96_issue.sql;
```

**What it shows**:
- SST Main record details
- All SST Details records with invoice information
- Summary comparison between `sst_details.amount` and `invoice.sst_inv`
- List of records that need fixing

### 2. `fix_sst_96_amounts.sql`
**Purpose**: SQL script to fix the SST amounts

**Usage**: Run this to fix the data
```sql
-- Step 1: Preview what will be updated (run this first)
-- Step 2: Update sst_details.amount from invoice.sst_inv
-- Step 3: Recalculate and update sst_main.amount
-- Step 4: Verify the fix
SOURCE fix_sst_96_amounts.sql;
```

**What it does**:
1. Updates `sst_details.amount` to match `invoice.sst_inv` where amount is 0, NULL, or doesn't match
2. Recalculates `sst_main.amount` to include both SST and reimbursement SST
3. Verifies the fix was successful

### 3. `fix_sst_96_amounts.php`
**Purpose**: PHP script to fix the SST amounts programmatically

**Usage**: Run from Laravel Tinker
```bash
php artisan tinker
require 'fix_sst_96_amounts.php';
```

**What it does**:
- Same as SQL script but uses Laravel models
- Provides detailed output of what was fixed
- Shows before/after values

## Recommended Fix Method

### Option 1: Using SQL (Recommended for quick fix)
1. Run `diagnose_sst_96_issue.sql` to see current state
2. Run `fix_sst_96_amounts.sql` to fix the data
3. Refresh the page: `http://127.0.0.1:8000/sst-v2-edit/96`

### Option 2: Using PHP Script
1. Run `php artisan tinker`
2. Run `require 'fix_sst_96_amounts.php';`
3. Refresh the page: `http://127.0.0.1:8000/sst-v2-edit/96`

### Option 3: Using Update Button (If controller logic is correct)
1. Go to `http://127.0.0.1:8000/sst-v2-edit/96`
2. Click "Update SST" button
3. The `updateSSTV2` method should recalculate amounts (lines 674-695)

**Note**: Option 3 may not work if the controller logic doesn't properly update existing records. The SQL/PHP scripts are more reliable.

## Expected Result

After running the fix:
- ✅ SST column should show the invoice's `sst_inv` amount (not 0.00)
- ✅ Reimb SST column should show remaining reimbursement SST
- ✅ Total SST column should show SST + Reimb SST
- ✅ Transfer Total Amount should match the sum of all Total SST values
- ✅ Total Amount at the top should show the correct grand total

## Verification

After running the fix, verify:
1. All SST amounts are > 0 (unless invoice actually has 0 SST)
2. Total Amount matches: Sum of (SST + Reimb SST) for all invoices
3. `sst_main.amount` matches the calculated total
4. Page displays correctly at `http://127.0.0.1:8000/sst-v2-edit/96`

## Prevention

To prevent this issue in the future:
- Ensure `createNewSSTRecordV2` properly sets `sst_details.amount` from `invoice.sst_inv`
- Ensure `updateSSTV2` properly updates `sst_details.amount` for existing records
- Add validation to ensure `sst_details.amount` is never 0 when `invoice.sst_inv` > 0

## Related Files

- `app/Http/Controllers/SSTV2Controller.php` - Controller with update logic
- `resources/views/dashboard/sst-v2/edit.blade.php` - View that displays the data
- `app/Models/SSTDetails.php` - Model for sst_details table












