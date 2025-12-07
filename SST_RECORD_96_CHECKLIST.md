# SST Record 96 Diagnostic Checklist

## Quick Check Methods

### Method 1: Run SQL Query
Run the SQL queries in `check_sst_96.sql` file in your database to see:
- SST Main record details
- All invoices in this SST record
- Calculated totals vs stored amount
- Any missing invoice references

### Method 2: Run PHP Script
Run the PHP script `check_sst_record_96.php` using Laravel Tinker:
```bash
php artisan tinker
```
Then:
```php
require 'check_sst_record_96.php';
```

### Method 3: Visual Check on Edit Page
Visit: http://127.0.0.1:8000/sst-v2-edit/96

Check the following:

#### A. Transfer Total Amount Field
- **Location**: Top section, "Transfer Total Amount" field
- **Expected**: Should show SST + Reimbursement SST for all invoices
- **Check**: Compare with the "Total SST" column footer in "Current Invoices" table

#### B. Current Invoices Table
- **Location**: "Current Invoices" section
- **Check each row**:
  - SST column: Should show the SST amount
  - Reimb SST column: Should show remaining reimbursement SST (reimbursement_sst - transferred_reimbursement_sst_amt)
  - Total SST column: Should be SST + Reimb SST
- **Check footer totals**:
  - Total SST footer should match "Transfer Total Amount" field
  - Should be sum of all SST + sum of all Reimb SST

#### C. New Transfer List (if any)
- **Location**: "New Transfer List" section
- **Check**: If there are invoices here, their totals should also be included in "Transfer Total Amount"

## Common Issues to Look For

### Issue 1: Amount Mismatch
- **Symptom**: "Transfer Total Amount" doesn't match the footer total
- **Cause**: Backend calculation might be wrong
- **Fix**: Click "Update SST" button to recalculate

### Issue 2: Missing Reimbursement SST
- **Symptom**: Reimb SST column shows 0.00 but should have values
- **Cause**: Reimbursement SST not calculated or transferred
- **Check**: Verify `reimbursement_sst` and `transferred_reimbursement_sst_amt` in invoice records

### Issue 3: Wrong Total Calculation
- **Symptom**: Total SST column doesn't equal SST + Reimb SST
- **Cause**: Calculation error in view
- **Fix**: Already fixed in recent updates

### Issue 4: Stored Amount vs Calculated Amount
- **Symptom**: Listing page shows different amount than edit page
- **Cause**: `sst_main.amount` not updated with reimbursement SST
- **Fix**: Click "Update SST" to save the correct total

## What to Report

If you find issues, please report:
1. **Transfer Total Amount** value shown on edit page
2. **Total SST** footer value in Current Invoices table
3. **Total SST Paid** value shown on listing page (if different)
4. **Number of invoices** in this SST record
5. **Any error messages** or unexpected values

## Quick Fix

If amounts don't match:
1. Go to edit page: http://127.0.0.1:8000/sst-v2-edit/96
2. Verify the "Transfer Total Amount" shows the correct value
3. Click "Update SST" button (even without making changes)
4. This will recalculate and save the correct total including reimbursement SST
5. Check the listing page to verify it now shows the correct amount





