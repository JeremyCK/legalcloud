# Server Deployment Checklist - SST Display Fix

## Issue
Updating SST value in invoice details page works, but the updated value doesn't reflect in case details invoice tab on server (works on local).

## Files Modified (Must Deploy to Server)

### 1. View Files (CRITICAL)
These view files need to be updated on the server to display custom SST values:

#### ✅ `resources/views/dashboard/case/tabs/bill/tab-invoice.blade.php`
**Location**: Line 179-229
**Change**: Added check for custom SST (`sst` column) before calculating
**Priority**: HIGH - This is the main invoice tab view

**What was changed:**
- Added priority check: Custom SST → ori_invoice_sst → Calculate
- Now checks `$details->sst` column first (the manually edited value)
- Falls back to calculation only if no custom SST exists

#### ✅ `resources/views/dashboard/case/table/tbl-case-invoice-p.blade.php`
**Location**: Line 40-115
**Change**: Added check for custom SST (`sst` column) before calculating
**Priority**: HIGH - This is used for invoice print/display

**What was changed:**
- Added robust SST value detection
- Checks for custom SST from database before calculating
- Fixed undefined variable `$isDebugDetail` error

### 2. Controller Files (Already Working - Invoice Save Works)
These are already working since invoice updates save correctly:

- `app/Http/Controllers/InvoiceController.php` - Already saves SST correctly
- `app/Http/Controllers/CaseController.php` - Already queries SST column

## Verification Steps

### Step 1: Check if files exist on server
SSH into server and verify these files exist:
```bash
ls -la resources/views/dashboard/case/tabs/bill/tab-invoice.blade.php
ls -la resources/views/dashboard/case/table/tbl-case-invoice-p.blade.php
```

### Step 2: Check file modification dates
Compare modification dates to ensure they're recent:
```bash
stat resources/views/dashboard/case/tabs/bill/tab-invoice.blade.php
stat resources/views/dashboard/case/table/tbl-case-invoice-p.blade.php
```

### Step 3: Verify the code changes
Check if the server files have the SST check code:
```bash
grep -n "Use custom SST from database" resources/views/dashboard/case/tabs/bill/tab-invoice.blade.php
grep -n "Use custom SST from database" resources/views/dashboard/case/table/tbl-case-invoice-p.blade.php
```

### Step 4: Clear view cache (if Laravel caches views)
```bash
php artisan view:clear
php artisan cache:clear
```

## Quick Fix Commands for Server

If files are missing or outdated, copy from local:

```bash
# From your local machine, copy files to server
scp resources/views/dashboard/case/tabs/bill/tab-invoice.blade.php user@server:/path/to/legalcloud/resources/views/dashboard/case/tabs/bill/
scp resources/views/dashboard/case/table/tbl-case-invoice-p.blade.php user@server:/path/to/legalcloud/resources/views/dashboard/case/table/
```

Or if using Git:
```bash
# On server
git pull origin main  # or your branch name
php artisan view:clear
php artisan cache:clear
```

## Testing After Deployment

1. Go to invoice details: `https://legal-cloud.co/invoice/9683/details`
2. Edit an SST value (e.g., change 4.00 to 4.01)
3. Save the invoice
4. Go to case details: `https://legal-cloud.co/case/{case_id}`
5. Check the invoice tab
6. Verify the SST shows the updated value (4.01) not the calculated value

## Additional Notes

- The backend code that saves SST is already working (invoice updates work)
- The issue is purely in the view files not checking for custom SST
- Both view files need the same fix: check `sst` column before calculating
- No database changes needed - the `sst` column already exists
