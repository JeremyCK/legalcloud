# Transfer Fee Columns - Database Table Mapping

## Overview
This document explains which database tables and columns are used for the 4 "to transfer" columns in the transfer fee edit page.

---

## The 4 Columns

### 1. **Pfee to transfer**
**Calculation:** `availablePfee = originalPfee - transferred_pfee_amt`

**Database Table:** `loan_case_invoice_main`

**Columns Used:**
- `pfee1_inv` - Professional fee 1
- `pfee2_inv` - Professional fee 2
- `transferred_pfee_amt` - Total professional fee already transferred (sum of all `transfer_fee_details.transfer_amount` for this invoice)

**Formula:**
```php
$originalPfee = ($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0);
$availablePfee = max(0, $originalPfee - ($detail->transferred_pfee_amt ?? 0));
```

**Location in Code:**
- View: `resources/views/dashboard/transfer-fee-v3/edit.blade.php` (lines 350-373)
- Controller: `app/Http/Controllers/TransferFeeV3Controller.php` (lines 954-959)

---

### 2. **SST to transfer**
**Calculation:** `availableSst = originalSst - transferred_sst_amt`

**Database Table:** `loan_case_invoice_main`

**Columns Used:**
- `sst_inv` - Sales and Service Tax for professional fees
- `transferred_sst_amt` - Total SST already transferred (sum of all `transfer_fee_details.sst_amount` for this invoice)

**Formula:**
```php
$originalSst = $detail->sst_inv ?? 0;
$availableSst = max(0, $originalSst - ($detail->transferred_sst_amt ?? 0));
```

**Location in Code:**
- View: `resources/views/dashboard/transfer-fee-v3/edit.blade.php` (lines 361-377)
- Controller: `app/Http/Controllers/TransferFeeV3Controller.php` (lines 955-960)

---

### 3. **Reimb to transfer**
**Calculation:** `availableReimbursement = originalReimbursement - transferred_reimbursement_amt`

**Database Table:** `loan_case_invoice_main`

**Columns Used:**
- `reimbursement_amount` - Reimbursement amount
- `transferred_reimbursement_amt` - Total reimbursement already transferred (sum of all `transfer_fee_details.reimbursement_amount` for this invoice)

**Formula:**
```php
$originalReimbursement = $detail->reimbursement_amount ?? 0;
$availableReimbursement = max(0, $originalReimbursement - ($detail->transferred_reimbursement_amt ?? 0));
```

**Location in Code:**
- View: `resources/views/dashboard/transfer-fee-v3/edit.blade.php` (lines 382-385)
- Controller: `app/Http/Controllers/TransferFeeV3Controller.php` (lines 956-961)

---

### 4. **Reimb SST to transfer**
**Calculation:** `availableReimbursementSst = originalReimbursementSst - transferred_reimbursement_sst_amt`

**Database Table:** `loan_case_invoice_main`

**Columns Used:**
- `reimbursement_sst` - Reimbursement SST amount
- `transferred_reimbursement_sst_amt` - Total reimbursement SST already transferred (sum of all `transfer_fee_details.reimbursement_sst_amount` for this invoice)

**Formula:**
```php
$originalReimbursementSst = $detail->reimbursement_sst ?? 0;
$availableReimbursementSst = max(0, $originalReimbursementSst - ($detail->transferred_reimbursement_sst_amt ?? 0));
```

**Location in Code:**
- View: `resources/views/dashboard/transfer-fee-v3/edit.blade.php` (lines 390-393)
- Controller: `app/Http/Controllers/TransferFeeV3Controller.php` (lines 957-962)

---

## Database Tables Summary

### Primary Table: `loan_case_invoice_main`

**Columns Used:**
1. `pfee1_inv` - Professional fee 1
2. `pfee2_inv` - Professional fee 2
3. `sst_inv` - Professional fee SST
4. `reimbursement_amount` - Reimbursement amount
5. `reimbursement_sst` - Reimbursement SST
6. `transferred_pfee_amt` - Total professional fee transferred (calculated from `transfer_fee_details`)
7. `transferred_sst_amt` - Total SST transferred (calculated from `transfer_fee_details`)
8. `transferred_reimbursement_amt` - Total reimbursement transferred (calculated from `transfer_fee_details`)
9. `transferred_reimbursement_sst_amt` - Total reimbursement SST transferred (calculated from `transfer_fee_details`)

### Supporting Table: `transfer_fee_details`

**Purpose:** Used to calculate the `transferred_*` amounts in `loan_case_invoice_main`

**Columns Used:**
- `loan_case_invoice_main_id` - Links to invoice
- `transfer_amount` - Professional fee transferred in this record
- `sst_amount` - SST transferred in this record
- `reimbursement_amount` - Reimbursement transferred in this record
- `reimbursement_sst_amount` - Reimbursement SST transferred in this record
- `transfer_fee_main_id` - Links to transfer fee main record

**How `transferred_*` amounts are calculated:**
```php
// Sum of all transfer_fee_details for this invoice
$invoice->transferred_pfee_amt = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
    ->where('status', '<>', 99)
    ->sum('transfer_amount');

$invoice->transferred_sst_amt = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
    ->where('status', '<>', 99)
    ->sum('sst_amount');

$invoice->transferred_reimbursement_amt = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
    ->where('status', '<>', 99)
    ->sum('reimbursement_amount');

$invoice->transferred_reimbursement_sst_amt = TransferFeeDetails::where('loan_case_invoice_main_id', $invoiceId)
    ->where('status', '<>', 99)
    ->sum('reimbursement_sst_amount');
```

---

## SQL Query Example

To check these values directly in the database:

```sql
SELECT 
    invoice_no,
    pfee1_inv,
    pfee2_inv,
    (pfee1_inv + pfee2_inv) as original_pfee,
    transferred_pfee_amt,
    (pfee1_inv + pfee2_inv - transferred_pfee_amt) as pfee_to_transfer,
    
    sst_inv as original_sst,
    transferred_sst_amt,
    (sst_inv - transferred_sst_amt) as sst_to_transfer,
    
    reimbursement_amount as original_reimbursement,
    transferred_reimbursement_amt,
    (reimbursement_amount - transferred_reimbursement_amt) as reimb_to_transfer,
    
    reimbursement_sst as original_reimbursement_sst,
    transferred_reimbursement_sst_amt,
    (reimbursement_sst - transferred_reimbursement_sst_amt) as reimb_sst_to_transfer
    
FROM loan_case_invoice_main
WHERE invoice_no IN ('DP20000817', 'DP20000816');
```

---

## Important Notes

1. **These are calculated values, not stored columns** - The "to transfer" amounts are calculated on-the-fly in the view/controller, not stored in the database.

2. **The `transferred_*` amounts are updated** when:
   - Transfer fee details are created/updated
   - Account tool fix runs
   - Invoice amounts are updated (via `InvoiceController::update()`)

3. **If "to transfer" shows 0.00**, it means:
   - The invoice has been fully transferred
   - `transferred_*_amt` = `original_*` amount

4. **The calculation excludes the current transfer fee record** when calculating available amounts in the edit view (to show what's available for THIS transfer, not all transfers).

---

## Files Reference

- **View:** `resources/views/dashboard/transfer-fee-v3/edit.blade.php`
  - Lines 350-393: Calculation of "to transfer" columns
  
- **Controller:** `app/Http/Controllers/TransferFeeV3Controller.php`
  - Method: `transferFeeEditV3()` (line 888)
  - Lines 934-972: Calculation of available amounts

