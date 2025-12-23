# Deployment Summary: SST Features and Invoice Editing

## Overview
This deployment adds the ability to edit SST values in the case details invoice tab, adds `ori_invoice_sst` column for tracking original SST totals, and implements cascading updates to transfer fees and ledger v2.

---

## 1. Database Changes

### Step 1.1: Add `ori_invoice_sst` Column
**File:** `add_ori_invoice_sst_column.sql` or run migration

**Option A: Run SQL Script**
```bash
mysql -u [username] -p [database_name] < add_ori_invoice_sst_column.sql
```

**Option B: Run Laravel Migration**
```bash
php artisan migrate
```
This will run: `database/migrations/2025_12_23_000001_add_ori_invoice_sst_column_to_loan_case_invoice_details.php`

**Verification:**
```sql
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'loan_case_invoice_details'
AND COLUMN_NAME = 'ori_invoice_sst';
```

---

## 2. Backfill Commands

### Step 2.1: Backfill `ori_invoice_sst` Values
**Command:** `php artisan invoice:backfill-ori-sst`

**Dry Run (Recommended First):**
```bash
php artisan invoice:backfill-ori-sst --dry-run
```

**Actual Run:**
```bash
php artisan invoice:backfill-ori-sst
```

**What it does:**
- Calculates `ori_invoice_sst` = `ori_invoice_amt * (sst_rate / 100)` from `loan_case_bill_main`
- Only updates taxable items (Professional fees and Reimbursement - categories 1 and 4)
- Applies special rounding rule (round down if 3rd decimal is 5)
- Skips records where `ori_invoice_sst` already exists (unless `--force` is used)

**Expected Output:**
- Found ~32,653 invoice details to process
- Updates all taxable invoice details with calculated SST values

**Force Update (if needed):**
```bash
php artisan invoice:backfill-ori-sst --force
```

---

## 3. Code Deployment

### Files Modified:

#### Backend:
1. **`app/Http/Controllers/CaseController.php`**
   - Added `updateInvoiceSST()` method
   - Updated `updateInvoiceValue()` to cascade updates
   - Added `updateTransferFeeAndLedgerForInvoice()` helper method
   - Added `updateTransferFeeMainAmt()` helper method
   - Added `updateLedgerEntriesForTransferFeeDetails()` helper method
   - Updated `loadCaseBill()` to select and use `ori_invoice_sst`

2. **`app/Http/Controllers/InvoiceController.php`**
   - Updated `update()` method to handle SST updates for split invoices
   - Updated `calculateInvoiceAmountsFromDetails()` to preserve manual SST values

#### Frontend:
3. **`resources/views/dashboard/case/tabs/bill/tab-invoice.blade.php`**
   - Updated to use `ori_invoice_sst` for display
   - Added edit button for SST column
   - Updated modal call to pass invoice amount and SST rate

4. **`resources/views/dashboard/case/d-bill-list.blade.php`**
   - Added modal for editing invoice SST
   - Added "Calculate" button with calculator icon

5. **`resources/views/dashboard/case/show.blade.php`**
   - Added `editInvoiceSSTModal()` function
   - Added `calculateInvoiceSST()` function
   - Added `updateInvoiceSST()` AJAX function

6. **`resources/views/dashboard/case/showv2.blade.php`**
   - Added `editInvoiceSSTModal()` function
   - Added `calculateInvoiceSST()` function
   - Added `updateInvoiceSST()` AJAX function

#### Routes:
7. **`routes/web.php`**
   - Added route: `Route::post('updateInvoiceSST', [CaseController::class, 'updateInvoiceSST']);`

---

## 4. Deployment Steps

### Step 4.1: Backup Database
```bash
mysqldump -u [username] -p [database_name] > backup_before_sst_deployment_$(date +%Y%m%d_%H%M%S).sql
```

### Step 4.2: Deploy Code Changes
```bash
# Pull latest code or deploy via your deployment method
git pull origin main
# or
# Deploy via your CI/CD pipeline
```

### Step 4.3: Run Database Migration
```bash
php artisan migrate
# OR run SQL script directly
mysql -u [username] -p [database_name] < add_ori_invoice_sst_column.sql
```

### Step 4.4: Run Backfill Command
```bash
# First, test with dry-run
php artisan invoice:backfill-ori-sst --dry-run

# If dry-run looks good, run actual backfill
php artisan invoice:backfill-ori-sst
```

### Step 4.5: Clear Cache (if applicable)
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### Step 4.6: Verify Deployment
1. **Check Column Exists:**
   ```sql
   DESCRIBE loan_case_invoice_details;
   -- Should show ori_invoice_sst column
   ```

2. **Check Backfill Results:**
   ```sql
   SELECT COUNT(*) as total,
          SUM(CASE WHEN ori_invoice_sst IS NOT NULL THEN 1 ELSE 0 END) as has_sst,
          SUM(CASE WHEN ori_invoice_sst IS NULL THEN 1 ELSE 0 END) as missing_sst
   FROM loan_case_invoice_details ild
   INNER JOIN account_item ai ON ild.account_item_id = ai.id
   WHERE ild.status <> 99
   AND ai.account_cat_id IN (1, 4);
   ```

3. **Test Functionality:**
   - Navigate to a case details page with invoices
   - Click the edit button (pencil icon) next to SST value
   - Verify modal opens with "Calculate" button
   - Click "Calculate" and verify SST is calculated correctly
   - Save and verify SST updates correctly
   - Check that split invoices update proportionally
   - Verify transfer fees and ledger v2 are updated (if applicable)

---

## 5. Rollback Plan (if needed)

### Rollback Database:
```sql
-- Remove ori_invoice_sst column (if needed)
ALTER TABLE `loan_case_invoice_details` DROP COLUMN `ori_invoice_sst`;
```

### Rollback Code:
```bash
git revert [commit-hash]
# or
git checkout [previous-commit]
```

---

## 6. Summary of Commands

```bash
# 1. Backup database
mysqldump -u [username] -p [database_name] > backup_$(date +%Y%m%d).sql

# 2. Deploy code
git pull origin main

# 3. Run migration
php artisan migrate
# OR
mysql -u [username] -p [database_name] < add_ori_invoice_sst_column.sql

# 4. Backfill SST values (dry-run first)
php artisan invoice:backfill-ori-sst --dry-run
php artisan invoice:backfill-ori-sst

# 5. Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 6. Verify
# Check database and test functionality
```

---

## 7. Important Notes

1. **Backfill Command:** The `invoice:backfill-ori-sst` command processes ~32,653 records. Run with `--dry-run` first to verify.

2. **Split Invoices:** When updating SST, the system now:
   - Updates `ori_invoice_sst` for all split invoices
   - Distributes SST proportionally to individual `sst` values
   - Updates transfer fees and ledger v2 automatically

3. **Calculate Button:** Uses the invoice amount (`ori_invoice_amt`) and SST rate from `loan_case_bill_main` to auto-calculate SST.

4. **Account Logging:** All SST changes are logged in the `account_log` table for audit purposes.

5. **Transfer Fee Updates:** When invoice SST is updated, related transfer fee details and ledger entries are automatically updated.

---

## 8. Files Created/Modified Summary

### New Files:
- `database/migrations/2025_12_23_000001_add_ori_invoice_sst_column_to_loan_case_invoice_details.php`
- `add_ori_invoice_sst_column.sql`
- `backfill_ori_invoice_sst.sql`
- `app/Console/Commands/BackfillOriInvoiceSST.php`
- `DEPLOYMENT_SUMMARY_SST_FEATURES.md` (this file)

### Modified Files:
- `app/Http/Controllers/CaseController.php`
- `app/Http/Controllers/InvoiceController.php`
- `resources/views/dashboard/case/tabs/bill/tab-invoice.blade.php`
- `resources/views/dashboard/case/d-bill-list.blade.php`
- `resources/views/dashboard/case/show.blade.php`
- `resources/views/dashboard/case/showv2.blade.php`
- `routes/web.php`

---

## 9. Testing Checklist

- [ ] Column `ori_invoice_sst` exists in database
- [ ] Backfill command completed successfully
- [ ] SST values are populated for taxable items
- [ ] Edit SST modal opens correctly
- [ ] Calculate button works correctly (100 × 8% = 8.00)
- [ ] SST updates save correctly
- [ ] Split invoices update proportionally
- [ ] Transfer fees update when invoice SST changes
- [ ] Ledger v2 updates when invoice SST changes
- [ ] Account log entries are created for SST changes

---

**Deployment Date:** _______________
**Deployed By:** _______________
**Verified By:** _______________

