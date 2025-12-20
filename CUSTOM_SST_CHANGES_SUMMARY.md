# Custom SST Feature - Changes Summary

## Overview
This document lists all files that were modified to support custom SST (Sales and Service Tax) values in invoice details. Users can now manually set SST values, and these custom values will be used instead of auto-calculated values throughout the application.

## Database Changes

### 1. Migration File
**File:** `database/migrations/2025_12_18_000001_add_sst_column_to_loan_case_invoice_details.php`
- **Purpose:** Adds `sst` column to `loan_case_invoice_details` table
- **Column Details:** 
  - Type: `DECIMAL(20,2)`
  - Nullable: Yes
  - Comment: Custom SST amount (if manually set, otherwise NULL to auto-calculate)
  - Position: After `amount` column

### 2. SQL Scripts (for manual execution on server)
- `add_sst_column_simple.sql` - Simple SQL to add the column
- `add_sst_column_to_invoice_details.sql` - SQL with error handling

---

## Controller Changes

### 3. InvoiceController.php
**File:** `app/Http/Controllers/InvoiceController.php`

**Changes:**
- **`update()` method:**
  - Added logic to save custom SST values to database
  - Added SST column existence check
  - Added logging for SST changes
  - Modified to use custom SST values in calculations
  - Added `$customSstValues` parameter to pass to calculation method

- **`getInvoiceDetails()` method:**
  - Updated query to conditionally select `sst` column based on column existence
  - Prevents crashes on servers without the migration

- **`calculateInvoiceAmountsFromDetails()` method:**
  - Added `$customSstValues` parameter
  - Updated to use custom SST values if provided
  - Falls back to calculation if no custom SST

### 4. CaseController.php
**File:** `app/Http/Controllers/CaseController.php`

**Changes:**
- **`loadCaseBill()` method:**
  - Updated invoice details query to explicitly include `sst` column
  - Added SST column existence check
  - Added debug logging for SST values

- **`calculateInvoiceAmountsFromDetails()` method:**
  - Updated to check for SST column existence
  - Updated to select and use custom SST values
  - Falls back to calculation if no custom SST

### 5. EInvoiceContoller.php
**File:** `app/Http/Controllers/EInvoiceContoller.php`

**Changes:**
- **`generateInvoicePDF()` method:**
  - Updated invoice details query to explicitly include `sst` column
  - Added SST column existence check

- **`loadBillToInvWIthInvoice()` method:**
  - Updated invoice details query to explicitly include `sst` column
  - Added SST column existence check

---

## View Changes

### 6. Invoice Details Page
**File:** `resources/views/dashboard/invoice/details.blade.php`

**Changes:**
- **`renderInvoiceDetails()` function:**
  - Updated to use `detail.sst` for SST input field value if available
  - Falls back to calculation if no custom SST
  - Added console logging for debugging

- **`saveInvoice()` function:**
  - Updated to send SST values in the request
  - Added console logging for debugging

### 7. Case Details - Invoice Table
**File:** `resources/views/dashboard/case/table/tbl-case-invoice-p.blade.php`

**Changes:**
- Updated SST calculation logic to check for custom SST value first
- Uses `property_exists()` and `isset()` to check for SST property
- Falls back to calculation with special rounding rules if no custom SST
- Added debug logging for troubleshooting

### 8. Case Details - Invoice Tab
**File:** `resources/views/dashboard/case/tabs/bill/tab-invoice.blade.php`

**Changes:**
- Updated SST calculation logic to check for custom SST value first
- Uses `property_exists()` to check for SST property
- Falls back to calculation with special rounding rules if no custom SST

### 9. Invoice Print View
**File:** `resources/views/dashboard/case/d-invoice-print.blade.php`

**Changes:**
- Updated SST calculation logic to check for custom SST value first
- Uses `property_exists()` to check for SST property
- Falls back to calculation with special rounding rules if no custom SST

### 10. Invoice Print PDF View
**File:** `resources/views/dashboard/case/d-invoice-print-pdf.blade.php`

**Changes:**
- Updated SST calculation logic to check for custom SST value first
- Uses `property_exists()` to check for SST property
- Falls back to calculation with special rounding rules if no custom SST

### 11. Invoice Download PDF Template
**File:** `resources/views/dashboard/case/d-invoice-print-simple.blade.php`

**Changes:**
- Updated SST calculation logic to check for custom SST value first
- Uses `property_exists()` to check for SST property
- Falls back to calculation with special rounding rules if no custom SST

---

## Key Features

1. **Backward Compatibility:**
   - All code checks if the `sst` column exists before using it
   - Works on servers where the migration hasn't been run yet
   - Falls back to auto-calculation if column doesn't exist

2. **Custom SST Saving:**
   - When user edits SST in invoice details page, it's saved to database
   - SST value is stored in `loan_case_invoice_details.sst` column
   - If SST is cleared, it's set to NULL to allow auto-calculation

3. **Consistent Display:**
   - Custom SST values are used consistently across:
     - Invoice details page
     - Case details invoice table
     - Case details invoice tab
     - Invoice print view
     - Invoice print PDF
     - Downloaded invoice PDF

4. **Calculation Fallback:**
   - If no custom SST is set (NULL), the system calculates SST using:
     - Special rounding rule: round DOWN if 3rd decimal is 5
     - Normal rounding otherwise

---

## Testing Checklist

- [ ] Invoice details page - Edit SST and verify it saves
- [ ] Case details invoice table - Verify custom SST displays correctly
- [ ] Case details invoice tab - Verify custom SST displays correctly
- [ ] Invoice print view - Verify custom SST displays correctly
- [ ] Invoice print PDF - Verify custom SST displays correctly
- [ ] Download invoice PDF - Verify custom SST displays correctly
- [ ] Verify SST reverts to auto-calculation when cleared
- [ ] Verify backward compatibility on servers without migration

---

## Migration Instructions

1. **Local Development:**
   ```bash
   php artisan migrate --path=database/migrations/2025_12_18_000001_add_sst_column_to_loan_case_invoice_details.php
   ```

2. **Production Server:**
   - Run the SQL script: `add_sst_column_simple.sql`
   - Or use the migration if possible

---

## Impact on Transfer Details and Ledger

### How It Works:
1. **Invoice SST Calculation:**
   - When custom SST is saved, `InvoiceController::update()` recalculates `sst_inv` in `loan_case_invoice_main`
   - The `calculateInvoiceAmountsFromDetails()` method uses custom SST values from invoice details
   - This updates the `sst_inv` field which is used by the transfer system

2. **Transfer Details:**
   - Transfer details store SST amount at time of transfer (in `TransferFeeDetails.sst_amount`)
   - Ledger v2 entries store SST amount at time of transfer (historical records)
   - **These historical records do NOT change** when SST is updated later (correct behavior)

3. **Remaining SST Calculation:**
   - Remaining SST = `sst_inv - transferred_sst_amt`
   - When SST is changed, `sst_inv` is recalculated, so remaining SST reflects the new value
   - This allows correct calculation of remaining SST to transfer

### Important Notes:
- ✅ **Transfer details records are NOT affected** - they store historical values (correct)
- ✅ **Ledger v2 entries are NOT affected** - they store historical values (correct)
- ✅ **Remaining SST calculation is updated** - uses new `sst_inv` value (correct)
- ✅ **Future transfers use new SST** - based on updated `sst_inv` value (correct)

### No Changes Needed:
- `TransferFeeV3Controller.php` - Already uses `sst_inv` from `loan_case_invoice_main`
- `TransferFeeDetails` table - Stores historical SST amounts (should not change)
- `LedgerEntriesV2` table - Stores historical SST amounts (should not change)

---

## Notes

- All changes maintain backward compatibility
- The `sst` column is nullable, so existing records are not affected
- Custom SST values take precedence over calculated values
- The special rounding rule (round down if 3rd decimal is 5) is preserved for calculated SST
- Transfer details and ledger entries store historical values and do not change when SST is updated
- Remaining SST to transfer is automatically recalculated when SST changes

