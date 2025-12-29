# Transfer Fee V3 - Total Amt & Collected Amt Calculation Guide

## Available Data Fields

### From `loan_case_invoice_main` (im) - Already Retrieved:
- ✅ `im.amount` → `$detail->invoice_amount` - **Invoice total amount** (most accurate for Total amt)
- ✅ `im.pfee1_inv` → `$detail->pfee1_inv` - Professional fee 1
- ✅ `im.pfee2_inv` → `$detail->pfee2_inv` - Professional fee 2  
- ✅ `im.sst_inv` → `$detail->sst_inv` - SST amount
- ✅ `im.reimbursement_amount` → `$detail->reimbursement_amount` - Reimbursement amount
- ✅ `im.reimbursement_sst` → `$detail->reimbursement_sst` - Reimbursement SST

### From `loan_case_bill_main` (b) - Already Retrieved:
- ✅ `b.total_amt` → `$detail->bill_total_amt` - **Bill total amount**
- ✅ `b.collected_amt` → `$detail->bill_collected_amt` - **Bill collected amount** (sum of voucher payments)

### Calculated Values:
- ✅ `$invoiceCount` - Number of invoices linked to the same bill
- ✅ `$detail->bill_total_amt_divided` - Currently calculated but then overwritten
- ✅ `$detail->bill_collected_amt_divided` - Currently calculated

## Current Calculation (Lines 1009-1018)

**Current Logic:**
```php
// Both Total amt and Collected amt use the SAME value
$totalAmount = $detail->bill_collected_amt ?? 0;
$calculatedAmount = round($totalAmount / $invoiceCount, 2);

$detail->bill_total_amt_divided = $calculatedAmount;  // Total amt
$detail->bill_collected_amt_divided = $calculatedAmount;  // Collected amt
```

**Result:** Both columns show the same value = `bill_collected_amt / invoice_count`

## How to Tweak the Calculation

### Option 1: Use Invoice Amount for Total Amt (Recommended)
**Total amt** = Invoice amount (from `loan_case_invoice_main.amount`)  
**Collected amt** = Bill collected amount divided by invoice count

```php
// Use invoice amount for Total amt (already calculated at line 984, but overwritten)
$detail->bill_total_amt_divided = round($detail->invoice_amount ?? 0, 2);

// Use collected amount divided by invoice count for Collected amt
$totalAmount = $detail->bill_collected_amt ?? 0;
$detail->bill_collected_amt_divided = round($totalAmount / $invoiceCount, 2);
```

### Option 2: Use Bill Total Amt for Total Amt
**Total amt** = Bill total amount divided by invoice count  
**Collected amt** = Bill collected amount divided by invoice count

```php
// Use bill total amount divided by invoice count for Total amt
$billTotal = $detail->bill_total_amt ?? 0;
$detail->bill_total_amt_divided = round($billTotal / $invoiceCount, 2);

// Use collected amount divided by invoice count for Collected amt
$totalAmount = $detail->bill_collected_amt ?? 0;
$detail->bill_collected_amt_divided = round($totalAmount / $invoiceCount, 2);
```

### Option 3: Calculate Total Amt from Components
**Total amt** = Sum of (pfee1 + pfee2 + sst + reimbursement + reimbursement_sst)  
**Collected amt** = Bill collected amount divided by invoice count

```php
// Calculate total from invoice components
$totalFromComponents = ($detail->pfee1_inv ?? 0) 
                     + ($detail->pfee2_inv ?? 0) 
                     + ($detail->sst_inv ?? 0) 
                     + ($detail->reimbursement_amount ?? 0) 
                     + ($detail->reimbursement_sst ?? 0);
$detail->bill_total_amt_divided = round($totalFromComponents, 2);

// Use collected amount divided by invoice count for Collected amt
$totalAmount = $detail->bill_collected_amt ?? 0;
$detail->bill_collected_amt_divided = round($totalAmount / $invoiceCount, 2);
```

### Option 4: Keep Current Logic (Both Same)
If you want both to remain the same, keep the current code as is.

## Implementation Location

**File:** `app/Http/Controllers/TransferFeeV3Controller.php`  
**Function:** `transferFeeEditV3()`  
**Lines:** 1009-1018

## Notes

1. **Invoice Amount (`im.amount`)** is the most accurate source for Total amt as it's calculated from invoice details
2. **Bill Collected Amt (`b.collected_amt`)** is the sum of all voucher payments for that bill
3. **Division by invoice count** is needed when multiple invoices share the same bill
4. The code at lines 982-1007 already calculates `bill_total_amt_divided` from invoice amount, but it's overwritten at line 1017

## Recommendation

**Use Option 1** - It's the most accurate because:
- Invoice amount is already stored and calculated correctly
- It matches what the invoice actually totals to
- Collected amount correctly reflects what was actually collected








