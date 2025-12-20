# Deployment Instructions for Custom SST and Editable Transfer Fee Amounts

## Overview

This deployment includes:
1. **Custom SST Feature**: Allows users to manually set SST amounts for invoice items without auto-recalculation
2. **Editable Transfer Fee Amounts**: Allows editing of `pfee`, `sst`, `reimb`, and `reimb sst` columns in transfer fee edit view
3. **Synchronization**: Updates transfer fee details, transfer fee main, ledger entries, and account logs when amounts are changed

## Files Changed

### Backend Controllers
- `app/Http/Controllers/InvoiceController.php`
  - Added `updateTransferFeeMainAmountsForInvoice()` method
  - Added `updateLedgerEntriesForTransferFeeDetails()` method
  - Modified `update()` method to sync transfer fee and ledger when invoice is updated
  - Modified `getInvoiceDetails()` to handle optional `sst` column
  - Modified `calculateInvoiceAmountsFromDetails()` to use custom SST values

- `app/Http/Controllers/CaseController.php`
  - Modified `loadCaseBill()` to select `sst` column if exists
  - Modified `calculateInvoiceAmountsFromDetails()` to use custom SST values

- `app/Http/Controllers/EInvoiceContoller.php`
  - Modified `generateInvoicePDF()` and `loadBillToInvWIthInvoice()` to select `sst` column

- `app/Http/Controllers/TransferFeeV3Controller.php`
  - Added `updateAmountsV3()` method (new API endpoint)
  - Added `updateBillTotalsFromInvoices()` method
  - Added `updateLedgerEntriesForTransferFeeDetails()` method
  - Added `addAccountLogEntry()` method
  - Modified `updateTransferFeeMainAmt()` to include status filtering and logging

### Frontend Views
- `resources/views/dashboard/invoice/details.blade.php`
  - Updated to use custom SST values

- `resources/views/dashboard/case/table/tbl-case-invoice-p.blade.php`
  - Updated to display custom SST values

- `resources/views/dashboard/case/tabs/bill/tab-invoice.blade.php`
  - Updated to display custom SST values

- `resources/views/dashboard/case/d-invoice-print.blade.php`
  - Updated to display custom SST values

- `resources/views/dashboard/case/d-invoice-print-pdf.blade.php`
  - Updated to display custom SST values

- `resources/views/dashboard/case/d-invoice-print-simple.blade.php`
  - Updated to display custom SST values

- `resources/views/dashboard/transfer-fee-v3/edit.blade.php`
  - Added inline editing for `pfee`, `sst`, `reimb`, and `reimb sst` columns
  - Added edit icons and JavaScript handlers
  - Removed Bootstrap modal dependency (using inline editing instead)

### Routes
- `routes/web.php`
  - Added route: `POST /transferfee/update-amounts/{detailId}` → `TransferFeeV3Controller@updateAmountsV3`

### Database Migration
- `database/migrations/2025_12_18_000001_add_sst_column_to_loan_case_invoice_details.php`
  - Adds `sst` column to `loan_case_invoice_details` table

## Deployment Steps

### Step 1: Backup Database

**CRITICAL**: Always backup your database before deployment.

```bash
# Example MySQL backup command
mysqldump -u username -p database_name > backup_before_sst_changes_$(date +%Y%m%d_%H%M%S).sql
```

### Step 2: Upload Code Files

Upload all changed files to your server. You can use:
- Git (if using version control)
- FTP/SFTP
- SCP

**Files to upload:**
```
app/Http/Controllers/InvoiceController.php
app/Http/Controllers/CaseController.php
app/Http/Controllers/EInvoiceContoller.php
app/Http/Controllers/TransferFeeV3Controller.php
resources/views/dashboard/invoice/details.blade.php
resources/views/dashboard/case/table/tbl-case-invoice-p.blade.php
resources/views/dashboard/case/tabs/bill/tab-invoice.blade.php
resources/views/dashboard/case/d-invoice-print.blade.php
resources/views/dashboard/case/d-invoice-print-pdf.blade.php
resources/views/dashboard/case/d-invoice-print-simple.blade.php
resources/views/dashboard/transfer-fee-v3/edit.blade.php
routes/web.php
database/migrations/2025_12_18_000001_add_sst_column_to_loan_case_invoice_details.php
```

### Step 3: Run Database Migration

**Option A: Using Laravel Migration (Recommended)**

```bash
# SSH into your server
ssh user@your-server

# Navigate to project directory
cd /path/to/legalcloud

# Run the migration
php artisan migrate
```

**Option B: Manual SQL (If migration doesn't work)**

If the migration file doesn't run, you can manually add the column:

```sql
-- Check if column exists first
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'your_database_name' 
  AND TABLE_NAME = 'loan_case_invoice_details' 
  AND COLUMN_NAME = 'sst';

-- If column doesn't exist, add it
ALTER TABLE `loan_case_invoice_details` 
ADD COLUMN `sst` DECIMAL(20,2) NULL 
COMMENT 'Custom SST amount (if manually set, otherwise NULL to auto-calculate)' 
AFTER `amount`;
```

### Step 4: Clear Laravel Caches

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# If using opcache
php artisan optimize:clear
```

### Step 5: Verify Route Registration

Check that the new route is registered:

```bash
php artisan route:list | grep updateAmounts
```

You should see:
```
POST transferfee/update-amounts/{detailId} ... TransferFeeV3Controller@updateAmountsV3
```

### Step 6: Test the Features

#### Test 1: Custom SST in Invoice Details
1. Go to an invoice details page: `/invoice/{id}/details`
2. Edit an item's SST value
3. Save and verify it persists
4. Check case details page to confirm SST is displayed correctly

#### Test 2: Editable Transfer Fee Amounts
1. Go to transfer fee edit page: `/transferfee/{id}/edit`
2. Click edit icon next to `pfee`, `sst`, `reimb`, or `reimb sst`
3. Update the value and save
4. Verify:
   - The value updates in the table
   - Transfer fee main totals update
   - Ledger entries are updated
   - Account log is created

#### Test 3: Invoice Print/Download
1. Go to case details page
2. Print or download an invoice
3. Verify SST values are correct in the print/download

### Step 7: Monitor Logs

After deployment, monitor Laravel logs for any errors:

```bash
tail -f storage/logs/laravel-$(date +%Y-%m-%d).log
```

## Rollback Instructions

If you need to rollback:

### Rollback Code Changes
1. Restore previous versions of all changed files from your backup/version control

### Rollback Database Migration
```bash
# Rollback the migration
php artisan migrate:rollback --step=1
```

Or manually remove the column:
```sql
ALTER TABLE `loan_case_invoice_details` DROP COLUMN `sst`;
```

**Note**: Rolling back will remove custom SST values. Existing custom SST values will be lost.

## Important Notes

1. **Backward Compatibility**: The code is designed to work even if the `sst` column doesn't exist (checks are in place), but the custom SST feature won't work without it.

2. **Existing Data**: Existing invoice details will have `sst = NULL`, which means they will continue to use auto-calculation until manually set.

3. **Transfer Fee Synchronization**: When you edit an invoice's SST, it automatically updates related transfer fee details and ledger entries. This happens automatically in the background.

4. **Permissions**: Only users with roles `admin`, `maker`, or `account` can edit transfer fee amounts, and only if the transfer fee is not reconciled (`is_recon != '1'`).

5. **No Bootstrap Modal Dependency**: The editable transfer fee feature uses inline editing, so it doesn't require Bootstrap modal JavaScript. This avoids the modal loading issues.

## Troubleshooting

### Issue: "Column not found: 1054 Unknown column 'id.sst'"
**Solution**: The migration hasn't been run. Run Step 3 above.

### Issue: Route not found
**Solution**: Clear route cache: `php artisan route:clear`

### Issue: Changes not reflecting
**Solution**: Clear all caches (Step 4) and restart your web server if using PHP-FPM.

### Issue: Modal not appearing (old issue, should be fixed)
**Solution**: This is now fixed - we use inline editing instead of modals.

## Support

If you encounter any issues during deployment, check:
1. Laravel logs: `storage/logs/laravel-*.log`
2. Web server error logs
3. Database error logs
4. Browser console for JavaScript errors

