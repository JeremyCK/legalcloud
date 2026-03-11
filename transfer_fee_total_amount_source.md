# Transfer Fee - Total Amount Source Explanation

## Overview
This document explains where the **Total amt** value comes from in the Transfer Fee invoice selection modal (`/transferfee/create`).

## Display Location
**View File:** `resources/views/dashboard/transfer-fee-v3/table/tbl-transfer-invoice-list.blade.php`  
**Line:** 169-170

## Formula
```php
Total Amount = pfee1_inv + pfee2_inv + sst_inv + invoice_reimbursement_amount + invoice_reimbursement_sst
```

## Data Source Chain

### 1. Database Table: `loan_case_invoice_main`
The Total amt is calculated from these fields stored in the `loan_case_invoice_main` table:
- `pfee1_inv` - Professional Fee 1 amount
- `pfee2_inv` - Professional Fee 2 amount  
- `sst_inv` - SST (Sales & Service Tax) amount
- `reimbursement_amount` - Reimbursement amount
- `reimbursement_sst` - Reimbursement SST amount

**Controller Query:** `app/Http/Controllers/TransferFeeV3Controller.php` (lines 335-339)
```php
'im.pfee1_inv', // Use invoice data directly
'im.pfee2_inv', // Use invoice data directly
'im.sst_inv',   // Use invoice data directly
'im.reimbursement_amount as invoice_reimbursement_amount',
'im.reimbursement_sst as invoice_reimbursement_sst',
```

### 2. How These Values Are Calculated

These invoice amounts are **calculated from invoice details** and stored in the `loan_case_invoice_main` table.

**Calculation Method:** `InvoiceController::calculateInvoiceAmountsFromDetails()`  
**File:** `app/Http/Controllers/InvoiceController.php` (lines 1137-1240)

#### Calculation Process:

1. **Get Invoice Details:**
   - Source: `loan_case_invoice_details` table
   - Joined with `account_item` table to get category information

2. **Calculate by Category:**
   - **Category 1 (Professional Fee):**
     - Sum amounts where `account_cat_id = 1`
     - Split into `pfee1` (where `pfee1_item = 1`) and `pfee2` (where `pfee1_item = 0`)
   
   - **Category 4 (Reimbursement):**
     - Sum amounts where `account_cat_id = 4`
     - Stored as `reimbursement_amount`
   
   - **SST Calculation:**
     - For Professional Fee: `SST = pfee1 × SST_rate + pfee2 × SST_rate`
     - For Reimbursement: `reimbursement_sst = reimbursement_amount × SST_rate`
     - SST rate comes from `loan_case_bill_main.sst_rate`

3. **Store Calculated Values:**
   - These calculated values are stored in `loan_case_invoice_main` table:
     ```php
     'pfee1_inv' => $invoiceCalculations['pfee1'],
     'pfee2_inv' => $invoiceCalculations['pfee2'],
     'sst_inv' => $invoiceCalculations['sst'],
     'reimbursement_amount' => $invoiceCalculations['reimbursement_amount'],
     'reimbursement_sst' => $invoiceCalculations['reimbursement_sst'],
     'amount' => $invoiceCalculations['total'], // Total of all above
     ```

### 3. When Are These Values Updated?

The invoice amounts are recalculated and updated when:
- Invoice is created
- Invoice details are modified
- Invoice is split
- Bill amounts are redistributed
- SST rate changes

**Update Methods:**
- `InvoiceController::updateInvoiceAmounts()` (line 1318)
- `CaseController::updateBillandCaseFigure()` (line 11509)
- `InvoiceController::calculateInvoiceAmountsFromDetails()` (line 1137)

## Example Calculation

For invoice showing Total amt = 5,489.55:

**Breakdown:**
- pfee1_inv: 4,222.92
- pfee2_inv: 0.00
- sst_inv: 337.83
- reimbursement_amount: 860.00
- reimbursement_sst: 68.80

**Total:** 4,222.92 + 0.00 + 337.83 + 860.00 + 68.80 = **5,489.55**

## Summary

**Total amt** comes from:
1. **Direct Source:** `loan_case_invoice_main` table fields (pfee1_inv, pfee2_inv, sst_inv, reimbursement_amount, reimbursement_sst)
2. **Original Source:** Calculated from `loan_case_invoice_details` table based on account categories
3. **Display:** Sum of all invoice amount fields displayed in the modal table

## Related Files

- **Controller:** `app/Http/Controllers/TransferFeeV3Controller.php` (method: `getTransferInvoiceListV3`)
- **View:** `resources/views/dashboard/transfer-fee-v3/table/tbl-transfer-invoice-list.blade.php`
- **Calculation:** `app/Http/Controllers/InvoiceController.php` (method: `calculateInvoiceAmountsFromDetails`)
- **Model:** `app/Models/LoanCaseInvoiceMain.php`
