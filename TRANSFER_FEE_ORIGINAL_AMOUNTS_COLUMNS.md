# Transfer Fee - Original Amount Columns Database Mapping

## Overview
This document explains which database tables and columns are used for the 4 **original invoice amount** columns in the transfer fee edit page:
1. **pfee** (Professional Fee)
2. **sst** (Sales and Service Tax)
3. **reimb** (Reimbursement)
4. **reimb sst** (Reimbursement SST)

---

## Database Table: `loan_case_invoice_main`

All 4 columns come from the **`loan_case_invoice_main`** table.

---

## Column Details

### 1. **pfee** (Professional Fee)
**Display Formula:** `pfee1_inv + pfee2_inv`

**Database Columns:**
- `pfee1_inv` - Professional fee 1
- `pfee2_inv` - Professional fee 2

**Code Location:**
- View: `resources/views/dashboard/transfer-fee-v3/edit.blade.php` (line 332)
```php
{{ number_format(($detail->pfee1_inv ?? 0) + ($detail->pfee2_inv ?? 0), 2) }}
```

- Controller: `app/Http/Controllers/TransferFeeV3Controller.php` (lines 915-916)
```php
'im.pfee1_inv',
'im.pfee2_inv',
```

---

### 2. **sst** (Sales and Service Tax)
**Display Formula:** `sst_inv`

**Database Column:**
- `sst_inv` - Sales and Service Tax for professional fees

**Code Location:**
- View: `resources/views/dashboard/transfer-fee-v3/edit.blade.php` (line 336)
```php
{{ number_format($detail->sst_inv ?? 0, 2) }}
```

- Controller: `app/Http/Controllers/TransferFeeV3Controller.php` (line 917)
```php
'im.sst_inv',
```

---

### 3. **reimb** (Reimbursement)
**Display Formula:** `reimbursement_amount`

**Database Column:**
- `reimbursement_amount` - Reimbursement amount

**Code Location:**
- View: `resources/views/dashboard/transfer-fee-v3/edit.blade.php` (line 340)
```php
{{ number_format($detail->reimbursement_amount ?? 0, 2) }}
```

- Controller: `app/Http/Controllers/TransferFeeV3Controller.php` (line 918)
```php
'im.reimbursement_amount',
```

---

### 4. **reimb sst** (Reimbursement SST)
**Display Formula:** `reimbursement_sst`

**Database Column:**
- `reimbursement_sst` - Reimbursement Sales and Service Tax

**Code Location:**
- View: `resources/views/dashboard/transfer-fee-v3/edit.blade.php` (line 344)
```php
{{ number_format($detail->reimbursement_sst ?? 0, 2) }}
```

- Controller: `app/Http/Controllers/TransferFeeV3Controller.php` (line 919)
```php
'im.reimbursement_sst',
```

---

## SQL Query to View These Columns

```sql
SELECT 
    invoice_no,
    pfee1_inv,
    pfee2_inv,
    (pfee1_inv + pfee2_inv) as pfee,
    sst_inv as sst,
    reimbursement_amount as reimb,
    reimbursement_sst as reimb_sst
FROM loan_case_invoice_main
WHERE invoice_no IN ('DP20000817', 'DP20000816');
```

---

## How These Values Are Loaded

In `TransferFeeV3Controller::transferFeeEditV3()` method:

```php
$TransferFeeDetails = TransferFeeDetails::where('transfer_fee_main_id', '=', $id)
    ->leftJoin('loan_case_invoice_main as im', 'im.id', '=', 'transfer_fee_details.loan_case_invoice_main_id')
    ->leftJoin('loan_case_bill_main as b', 'b.id', '=', 'transfer_fee_details.loan_case_main_bill_id')
    ->leftJoin('loan_case as l', 'l.id', '=', 'b.case_id')
    ->leftJoin('client as c', 'c.id', '=', 'l.customer_id')
    ->leftJoin('invoice_billing_party as ibp', 'ibp.id', '=', 'im.bill_party_id')
    ->select(
        'transfer_fee_details.*',
        'im.invoice_no',
        DB::raw('DATE(im.Invoice_date) as invoice_date'),
        'im.amount as invoice_amount',
        'im.transferred_pfee_amt',
        'im.transferred_sst_amt',
        'im.pfee1_inv',        // ← Used for pfee column
        'im.pfee2_inv',        // ← Used for pfee column
        'im.sst_inv',          // ← Used for sst column
        'im.reimbursement_amount',  // ← Used for reimb column
        'im.reimbursement_sst',      // ← Used for reimb sst column
        'im.transferred_reimbursement_amt',
        'im.transferred_reimbursement_sst_amt',
        // ... other fields
    )
    ->get();
```

---

## Summary

| Column Name | Database Table | Database Columns | Formula |
|------------|----------------|------------------|---------|
| **pfee** | `loan_case_invoice_main` | `pfee1_inv`, `pfee2_inv` | `pfee1_inv + pfee2_inv` |
| **sst** | `loan_case_invoice_main` | `sst_inv` | `sst_inv` |
| **reimb** | `loan_case_invoice_main` | `reimbursement_amount` | `reimbursement_amount` |
| **reimb sst** | `loan_case_invoice_main` | `reimbursement_sst` | `reimbursement_sst` |

---

## Important Notes

1. **All values come from `loan_case_invoice_main` table** - These are the current invoice amounts stored in the database.

2. **These values are updated when:**
   - Invoice details are updated via `InvoiceController::update()`
   - Account tool fix runs (`InvoiceFixController::fixInvoice()`)
   - Invoice amounts are recalculated from details

3. **These are NOT calculated from `transfer_fee_details`** - They are the original invoice amounts, independent of transfer records.

4. **The view displays these directly** - No calculation needed, just format the values from the database.

---

## Files Reference

- **View:** `resources/views/dashboard/transfer-fee-v3/edit.blade.php`
  - Lines 332-345: Display of the 4 original amount columns
  
- **Controller:** `app/Http/Controllers/TransferFeeV3Controller.php`
  - Method: `transferFeeEditV3()` (line 888)
  - Lines 915-919: Selection of columns from `loan_case_invoice_main`

