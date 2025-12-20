# Custom SST and Transfer Fee Updates - Complete Changes Summary

## Overview
This document summarizes all changes made to support custom SST (Sales and Service Tax) values for invoice details and ensure transfer fee records reflect updated invoice SST values.

## Problem Statement
1. Users needed to manually update SST values without automatic recalculation
2. When invoice SST values were updated, transfer fee records still showed old historical values
3. Transfer fee main totals needed to reflect current invoice SST values

## Database Changes

### Migration: Add SST Column to Invoice Details
**File:** `database/migrations/2025_12_18_000001_add_sst_column_to_loan_case_invoice_details.php`

- Added `sst` column (decimal 20,2, nullable) to `loan_case_invoice_details` table
- Column stores custom SST amount if manually set, otherwise NULL for auto-calculation
- Positioned after `amount` column

**SQL for manual application:**
```sql
ALTER TABLE `loan_case_invoice_details` 
ADD COLUMN `sst` DECIMAL(20,2) NULL COMMENT 'Custom SST amount (if manually set, otherwise NULL to auto-calculate)' 
AFTER `amount`;
```

---

## Backend Changes

### 1. InvoiceController.php

#### Changes Made:

**a) Added Imports:**
```php
use App\Models\TransferFeeMain;
use App\Models\TransferFeeDetails;
```

**b) Modified `update()` Method:**
- Added logging at start of method
- Rounds `newAmount` and `customSst` to 2 decimal places
- Saves custom SST value to `existingDetail->sst` if provided
- Sets `existingDetail->sst` to `null` if no custom SST provided (allows auto-calculation)
- Triggers save if `amountChanged` OR `sstChanged`
- Added extensive logging for SST and amount changes
- Calls `updateTransferFeeMainAmountsForInvoice()` after updating invoice

**c) Modified `getInvoiceDetails()` Method:**
- Conditionally selects `id.sst` column based on database column existence
- Prevents crashes on servers without migration applied

**d) Modified `calculateInvoiceAmountsFromDetails()` Method:**
- Checks for `sst` column existence at start
- For professional fees (account_cat_id == 1):
  - Uses custom SST from request if provided
  - Otherwise checks database for saved custom SST
  - Otherwise calculates SST with special rounding rule (round down if 3rd decimal is 5)
- For reimbursement items (account_cat_id == 4):
  - Uses custom SST from request if provided
  - Otherwise checks database for saved custom SST (`$detail->sst`)
  - Otherwise calculates SST with special rounding rule
- Added debug logging for reimbursement SST calculation (detail ID 168726)

**e) Added `updateTransferFeeMainAmountsForInvoice()` Method:**
- Finds all transfer fee details for the invoice
- Updates `transfer_fee_details.reimbursement_sst_amount` to match current invoice `reimbursement_sst`
- Updates `transfer_fee_details.sst_amount` to match current invoice `sst_inv` if changed
- Handles single and multiple transfer records proportionally
- Recalculates transfer fee main amounts after updates
- Added comprehensive logging

**f) Added `updateTransferFeeMainAmt()` Method:**
- Sums all transfer fee details components:
  - `transfer_amount` (professional fee)
  - `sst_amount` (professional fee SST)
  - `reimbursement_amount`
  - `reimbursement_sst_amount`
- Updates `transfer_fee_main.transfer_amount` with total
- Logs changes when amount differs significantly

---

### 2. CaseController.php

#### Changes Made:

**a) Added Import:**
```php
use Illuminate\Support\Facades\Log;
```

**b) Modified `loadCaseBill()` Method:**
- Explicitly selects `qd.sst` column if it exists
- Ensures SST value is passed to views

**c) Modified `calculateInvoiceAmountsFromDetails()` Method:**
- Checks for `sst` column existence at start
- For reimbursement items (account_cat_id == 4):
  - Uses custom SST from database (`$detail->sst`) if available
  - Otherwise calculates SST with special rounding rule
- Same logic as InvoiceController for consistency

---

### 3. EInvoiceContoller.php

#### Changes Made:

**a) Modified `generateInvoicePDF()` Method:**
- Checks for `sst` column existence
- Explicitly selects `qd.sst` column if it exists
- Ensures custom SST values are available for PDF generation

**b) Modified `loadBillToInvWIthInvoice()` Method:**
- Checks for `sst` column existence
- Explicitly selects `qd.sst` column if it exists
- Ensures custom SST values are available for invoice print views

---

## Frontend/View Changes

### 1. invoice/details.blade.php

#### Changes Made:
- Modified `renderInvoiceDetails()` function:
  - Uses `detail.sst` for SST input field value if available
  - Otherwise calculates SST
- Added console logs in `saveInvoice()` for debugging

---

### 2. case/table/tbl-case-invoice-p.blade.php

#### Changes Made:
- Updated SST calculation logic:
  - Checks for custom SST value from `$details->sst` property
  - Uses custom SST if available and not empty
  - Otherwise calculates SST with special rounding rule
- Added debug logging for detail ID 168726
- Uses `\Log::info()` for Laravel logging consistency

---

### 3. case/tabs/bill/tab-invoice.blade.php

#### Changes Made:
- Updated SST calculation logic:
  - Checks for custom SST value from `$details->sst` property
  - Uses custom SST if available
  - Otherwise calculates SST with special rounding rule
- Same logic as `tbl-case-invoice-p.blade.php` for consistency

---

### 4. case/d-invoice-print.blade.php

#### Changes Made:
- Updated SST calculation logic:
  - Checks for custom SST value from `$details->sst` property
  - Uses custom SST if available
  - Otherwise calculates SST with special rounding rule
- Ensures print view shows correct custom SST values

---

### 5. case/d-invoice-print-pdf.blade.php

#### Changes Made:
- Updated SST calculation logic:
  - Checks for custom SST value from `$details->sst` property
  - Uses custom SST if available
  - Otherwise calculates SST with special rounding rule
- Ensures PDF print view shows correct custom SST values

---

### 6. case/d-invoice-print-simple.blade.php

#### Changes Made:
- Updated SST calculation logic:
  - Checks for custom SST value from `$details->sst` property
  - Uses custom SST if available
  - Otherwise calculates SST with special rounding rule
- Ensures downloaded invoice PDF shows correct custom SST values

---

## Key Features

### 1. Custom SST Storage
- SST values can be manually entered and saved
- Stored in `loan_case_invoice_details.sst` column
- NULL value triggers auto-calculation
- Preserved across invoice updates

### 2. Special Rounding Rule
- If SST calculation's 3rd decimal is 5, round DOWN
- Otherwise use normal rounding to 2 decimal places
- Applied consistently across all calculations

### 3. Transfer Fee Synchronization
- When invoice SST is updated, transfer fee details are automatically updated
- Transfer fee main totals recalculated to match sum of all details
- Handles single and multiple transfer records proportionally
- "Transferred SST" column reflects current invoice values, not historical

### 4. Backward Compatibility
- Code checks for `sst` column existence before using it
- Prevents crashes on servers without migration applied
- Gracefully falls back to auto-calculation if column doesn't exist

---

## Affected Areas

### Invoice Management
- ✅ Invoice details page (`/invoice/{id}/details`)
- ✅ Invoice update API endpoint
- ✅ Invoice amount calculations

### Case Management
- ✅ Case details page (`/case/{id}`)
- ✅ Invoice tab in case details
- ✅ Invoice table in case details
- ✅ Invoice print views

### Transfer Fee Management
- ✅ Transfer fee edit page (`/transferfee/{id}/edit`)
- ✅ Transfer fee details table
- ✅ Transfer fee main totals
- ✅ "Transferred SST" column calculation

### PDF Generation
- ✅ Invoice print PDF
- ✅ Invoice download PDF
- ✅ Simple invoice print PDF

---

## Testing Checklist

- [x] Save invoice with custom SST value
- [x] Verify SST persists after save
- [x] Verify SST displays correctly in case details
- [x] Verify SST displays correctly in invoice tab
- [x] Verify SST displays correctly in print views
- [x] Verify SST displays correctly in PDF downloads
- [x] Verify transfer fee details update when invoice SST changes
- [x] Verify transfer fee main totals recalculate correctly
- [x] Verify "Transferred SST" column shows updated values
- [x] Verify backward compatibility (works without migration)

---

## Files Modified

### Controllers
1. `app/Http/Controllers/InvoiceController.php`
2. `app/Http/Controllers/CaseController.php`
3. `app/Http/Controllers/EInvoiceContoller.php`

### Views
1. `resources/views/dashboard/invoice/details.blade.php`
2. `resources/views/dashboard/case/table/tbl-case-invoice-p.blade.php`
3. `resources/views/dashboard/case/tabs/bill/tab-invoice.blade.php`
4. `resources/views/dashboard/case/d-invoice-print.blade.php`
5. `resources/views/dashboard/case/d-invoice-print-pdf.blade.php`
6. `resources/views/dashboard/case/d-invoice-print-simple.blade.php`

### Database
1. `database/migrations/2025_12_18_000001_add_sst_column_to_loan_case_invoice_details.php`

---

## Deployment Notes

1. **Run Migration:**
   ```bash
   php artisan migrate --path=database/migrations/2025_12_18_000001_add_sst_column_to_loan_case_invoice_details.php
   ```

2. **Or Apply SQL Manually:**
   ```sql
   ALTER TABLE `loan_case_invoice_details` 
   ADD COLUMN `sst` DECIMAL(20,2) NULL COMMENT 'Custom SST amount (if manually set, otherwise NULL to auto-calculate)' 
   AFTER `amount`;
   ```

3. **Clear Cache (if needed):**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

---

## Example Scenario

**Before:**
- Invoice reimbursement SST: 67.55 (auto-calculated)
- Transfer fee detail reimbursement SST: 67.55
- Transferred SST: 155.55 (88.00 + 67.55)

**After Update:**
- User updates invoice reimbursement SST to: 67.56
- Invoice reimbursement SST: 67.56 (custom)
- Transfer fee detail reimbursement SST: 67.56 (automatically updated)
- Transferred SST: 155.56 (88.00 + 67.56) ✅

---

## Logging

All changes include comprehensive logging:
- Invoice update operations
- SST value changes
- Transfer fee detail updates
- Transfer fee main recalculations
- Debug logs for troubleshooting

Check `storage/logs/laravel-YYYY-MM-DD.log` for detailed logs.

---

## Notes

- Transfer fee details are historical records but are updated to reflect current invoice values
- Custom SST values take precedence over auto-calculated values
- All calculations maintain 2 decimal precision
- Special rounding rule (round down if 3rd decimal is 5) is applied consistently

