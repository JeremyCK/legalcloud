# Server Deployment Checklist for SST Record 96 Fix

## Issue
On the server, SST column shows 0.00 for all invoices in SST Record 96.

## Root Cause
The `sst_details.amount` field is 0 or NULL. It needs to be updated from `invoice.sst_inv`.

## Required Actions

### 1. Run SQL Patch on Server

**File:** `SERVER_PATCH_REQUIRED.sql`

This will:
- Update `sst_details.amount` from `invoice.sst_inv`
- Recalculate `sst_main.amount` to include both SST and reimbursement SST

**How to run:**
```sql
SOURCE SERVER_PATCH_REQUIRED.sql;
```

Or copy and paste the SQL statements into your MySQL client.

### 2. Deploy Code Changes

**Files to deploy:**
1. `resources/views/dashboard/sst-v2/edit.blade.php`
   - Changed to show full `reimbursement_sst` (not remaining)
   
2. `app/Http/Controllers/SSTV2Controller.php`
   - Updated CREATE and UPDATE methods to include full reimbursement SST in total

### 3. Clear Cache (After Code Deployment)

```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### 4. Verify

After running SQL patch and deploying code:
1. Refresh the page: `http://your-server/sst-v2-edit/96`
2. Check SST column - should show values > 0
3. Check Reimb SST column - should show values > 0
4. Check Total Amount - should include both SST and Reimb SST

## Quick Fix (If Only SST is Missing)

If only SST is missing (Reimb SST is showing correctly), just run:

```sql
-- Quick fix for SST amounts only
UPDATE sst_details sd
INNER JOIN loan_case_invoice_main im ON im.id = sd.loan_case_invoice_main_id
SET sd.amount = COALESCE(im.sst_inv, 0)
WHERE sd.sst_main_id = 96
AND (sd.amount = 0 OR sd.amount IS NULL);
```

## Summary

**SQL Patch Required:** ✅ YES - Run `SERVER_PATCH_REQUIRED.sql`
**Code Deployment:** ✅ YES - Deploy the 2 files mentioned above
**Cache Clear:** ✅ YES - Clear Laravel cache after deployment










