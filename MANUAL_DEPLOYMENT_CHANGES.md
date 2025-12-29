# Manual Deployment - Files Changed for Editable Transfer Fee Amounts

## Files to Deploy to Server

### 1. **View File** (CRITICAL - Most Important)
**File:** `resources/views/dashboard/transfer-fee-v3/edit.blade.php`

**Changes Made:**
- Added edit icons for `pfee`, `sst`, `reimb`, and `reimb sst` columns (lines ~342, 363, 382, 401)
- Each icon has these data attributes:
  - `data-detail-id`
  - `data-invoice-id`
  - `data-bill-id` (recently added)
  - `data-pfee1` and `data-pfee2` (for pfee only)
- Added JavaScript handlers for inline editing:
  - `.edit-pfee` handler (line ~3417)
  - `.edit-sst` handler (line ~3601)
  - `.edit-reimb` handler (line ~3612)
  - `.edit-reimb-sst` handler (line ~3623)
  - `editAmountInline()` function (line ~3631)
- Removed Bootstrap modal code (replaced with inline editing)
- All icons are inside permission check: `@if(in_array(auth()->user()->menuroles, ['admin', 'maker', 'account']) && $TransferFeeMain->is_recon != '1')`

**What to do:**
- Upload this entire file to server
- Replace existing file at: `resources/views/dashboard/transfer-fee-v3/edit.blade.php`

---

### 2. **Controller File**
**File:** `app/Http/Controllers/TransferFeeV3Controller.php`

**Changes Made:**
- Added `updateAmountsV3()` method (line ~2445)
  - Handles AJAX requests to update pfee, sst, reimb, reimb_sst
  - Updates `loan_case_invoice_main` table
  - Updates `transfer_fee_details` table
  - Updates `loan_case_bill_main` totals
  - Updates `transfer_fee_main` totals
  - Updates `ledger_entries_v2` table
  - Creates `account_log` entries
- Added `updateBillTotalsFromInvoices()` method (line ~2907)
  - Recalculates bill totals from all invoices
- Added `updateLedgerEntriesForTransferFeeDetails()` method (line ~2700)
  - Updates or creates ledger entries for transfer fee details
- Added `addAccountLogEntry()` method
  - Creates account log entries for changes
- Modified `updateTransferFeeMainAmt()` method (line ~808)
  - Added status filtering (`where('status', '<>', 99)`)
  - Added logging
  - Added null checks

**What to do:**
- Upload this entire file to server
- Replace existing file at: `app/Http/Controllers/TransferFeeV3Controller.php`

---

### 3. **Route File**
**File:** `routes/web.php`

**Changes Made:**
- Added new route (around line 1272):
```php
Route::post('/update-amounts/{detailId}', [TransferFeeV3Controller::class, 'updateAmountsV3'])->name('transferfee.updateAmounts');
```

**What to do:**
- Find the `transferfee` route group in `routes/web.php`
- Add the route above inside that group
- OR upload the entire `routes/web.php` file if you're not sure where to add it

---

## Files NOT Needed for Deployment (Debug/Helper Files)

These files were created for debugging/documentation and are NOT needed on server:
- ❌ `diagnose_edit_icons.php` (debug script)
- ❌ `verify_edit_icons.php` (verification script)
- ❌ `debug_edit_icons.blade.php` (debug template)
- ❌ `TROUBLESHOOTING_EDIT_ICONS.md` (documentation)
- ❌ `QUICK_FIX_EDIT_ICONS.md` (documentation)
- ❌ `SERVER_DEBUG_STEPS.md` (documentation)
- ❌ `FILES_TO_COMMIT_CHECKLIST.md` (documentation)
- ❌ `DEPLOYMENT_FILES_SUMMARY.md` (documentation)
- ❌ `MANUAL_DEPLOYMENT_CHANGES.md` (this file - documentation only)

---

## Deployment Steps

### Step 1: Backup Current Files
```bash
# On server, backup existing files
cp resources/views/dashboard/transfer-fee-v3/edit.blade.php resources/views/dashboard/transfer-fee-v3/edit.blade.php.backup
cp app/Http/Controllers/TransferFeeV3Controller.php app/Http/Controllers/TransferFeeV3Controller.php.backup
cp routes/web.php routes/web.php.backup
```

### Step 2: Upload Files
Upload these 3 files from your local machine to server:
1. `resources/views/dashboard/transfer-fee-v3/edit.blade.php`
2. `app/Http/Controllers/TransferFeeV3Controller.php`
3. `routes/web.php`

### Step 3: Verify File Sizes
```bash
# On server, check file sizes match
ls -lh resources/views/dashboard/transfer-fee-v3/edit.blade.php
ls -lh app/Http/Controllers/TransferFeeV3Controller.php
ls -lh routes/web.php
```

### Step 4: Clear All Caches
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
php artisan route:clear
rm -rf storage/framework/views/*
php artisan optimize:clear
```

### Step 5: Verify Route
```bash
php artisan route:list | grep updateAmounts
```

Should show:
```
POST transferfee/update-amounts/{detailId} ... TransferFeeV3Controller@updateAmountsV3
```

### Step 6: Test
1. Go to transfer fee edit page: `/transferfee/487/edit`
2. Check if edit icons appear next to pfee, sst, reimb, reimb sst columns
3. Click an icon - should show input field
4. Enter value and save - should update successfully

---

## Quick Verification

After deployment, check HTML source:
1. View page source (Ctrl+U)
2. Search for `edit-pfee`
3. Should see:
```html
<i class="fa fa-pencil edit-pfee ml-1" 
   style="cursor: pointer; color: #007bff; font-size: 11px;" 
   data-detail-id="..."
   data-invoice-id="..."
   data-bill-id="..."
   ...>
```

If not found, clear caches again or check file was uploaded correctly.

---

## Summary

**Files to Deploy:**
1. ✅ `resources/views/dashboard/transfer-fee-v3/edit.blade.php`
2. ✅ `app/Http/Controllers/TransferFeeV3Controller.php`
3. ✅ `routes/web.php`

**No Database Changes Required** ✅

**After Deployment:**
- Clear all caches
- Verify route
- Test functionality




