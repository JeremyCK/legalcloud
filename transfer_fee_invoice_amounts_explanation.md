# Transfer Fee - Invoice Modal: Collected Amount & Total Amount Source

## Overview
This document explains how **Collected Amount** and **Total Amount** are retrieved and displayed in the Select Invoice Modal for Transfer Fee creation (`/transferfee/create`).

## Route & Controller Method

**Route:** `GET /transferfee/invoice-list`  
**Route Name:** `transferfee.invoice-list`  
**Controller:** `TransferFeeV3Controller@getTransferInvoiceListV3`  
**File:** `app/Http/Controllers/TransferFeeV3Controller.php` (lines 298-549)

## Data Source

### Database Tables Used:
1. **`loan_case_invoice_main`** (alias: `im`) - Primary table for invoice data
2. **`loan_case_bill_main`** (alias: `b`) - Bill information table
3. **`loan_case`** (alias: `l`) - Case information
4. **`client`** (alias: `c`) - Client information
5. **`invoice_billing_party`** (alias: `ibp`) - Billing party information

### Fields Selected from Database:

**From `loan_case_invoice_main` table:**
- `im.pfee1_inv` - Professional fee 1
- `im.pfee2_inv` - Professional fee 2
- `im.sst_inv` - SST amount
- `im.reimbursement_amount` - Reimbursement amount
- `im.reimbursement_sst` - Reimbursement SST
- `im.transferred_pfee_amt` - Already transferred professional fee
- `im.transferred_sst_amt` - Already transferred SST
- `im.transferred_reimbursement_amt` - Already transferred reimbursement
- `im.transferred_reimbursement_sst_amt` - Already transferred reimbursement SST

**From `loan_case_bill_main` table:**
- `b.collected_amt as bill_collected_amt` ⚠️ **NOT CURRENTLY USED**
- `b.total_amt as bill_total_amt` ⚠️ **NOT CURRENTLY USED**

## Current Implementation (UPDATED)

### How Values are Calculated:

**Total Amount** (Displayed in table column "Total amt"):
```php
Total Amount = pfee1_inv + pfee2_inv + sst_inv + invoice_reimbursement_amount + invoice_reimbursement_sst
```
- **Source:** Calculated from `loan_case_invoice_main` table fields
- **Location:** `resources/views/dashboard/transfer-fee-v3/table/tbl-transfer-invoice-list.blade.php` (line 169-170)

**Collected Amount** (Displayed in table column "Collected amt"):
```php
Collected Amount = bill_collected_amt / invoice_count_per_bill
```
- **Source:** `loan_case_bill_main.collected_amt` divided by number of invoices on the same bill
- **Logic:** 
  - If bill has only 1 invoice: `invoice_collected_amt = bill_collected_amt`
  - If bill has multiple invoices: `invoice_collected_amt = bill_collected_amt / invoice_count`
- **Location:** 
  - **Controller:** `app/Http/Controllers/TransferFeeV3Controller.php` (lines 526-555)
  - **View:** `resources/views/dashboard/transfer-fee-v3/table/tbl-transfer-invoice-list.blade.php` (line 172-173)

### Code Reference:

**Controller Calculation:**
```php
// Count invoices per bill
$billInvoiceCounts[$billId] = DB::table('loan_case_invoice_main')
    ->where('loan_case_main_bill_id', $billId)
    ->where('status', '<>', 99)
    ->count();

// Calculate collected amount per invoice
if ($invoiceCount == 1) {
    $row->invoice_collected_amt = round($billCollectedAmt, 2);
} else {
    $row->invoice_collected_amt = round($billCollectedAmt / $invoiceCount, 2);
}
```

**View Display:**
```php
// Total amt (line 169-170)
{{ number_format(($row->pfee1_inv ?? 0) + ($row->pfee2_inv ?? 0) + ($row->sst_inv ?? 0) + ($row->invoice_reimbursement_amount ?? 0) + ($row->invoice_reimbursement_sst ?? 0), 2, '.', ',') }}

// Collected amt (line 172-173) - Now uses bill collected amount
{{ number_format($row->invoice_collected_amt ?? 0, 2, '.', ',') }}
```

## Important Notes

1. **Total Amount** comes from invoice amounts (pfee1_inv + pfee2_inv + sst_inv + reimbursement)
2. **Collected Amount** now correctly comes from `bill_collected_amt` divided by invoice count per bill
3. **Bill collected amount** is calculated from vouchers (receipts) - see `VoucherControllerV2::updateTotalFigureBillTrust()` (line 810)
4. **Multiple invoices per bill** - If a bill has multiple invoices, the collected amount is divided equally among them

## AJAX Call Flow

1. **Frontend:** `loadMainInvoiceList()` function in `create.blade.php` (line 649)
2. **AJAX Request:** Calls route `transferfee.invoice-list` with search parameters
3. **Backend:** `getTransferInvoiceListV3()` method processes the request
4. **Response:** Returns HTML table rendered from `tbl-transfer-invoice-list.blade.php` view
5. **Display:** Table is inserted into modal via `$('#invoiceListContainer').html(response.invoiceList)`

## Related Files

- **Controller:** `app/Http/Controllers/TransferFeeV3Controller.php` (method: `getTransferInvoiceListV3`)
- **View:** `resources/views/dashboard/transfer-fee-v3/table/tbl-transfer-invoice-list.blade.php`
- **Main Create Page:** `resources/views/dashboard/transfer-fee-v3/create.blade.php`
- **Route:** `routes/web.php` (line 1276)
