# Transferred SST Discrepancy Explanation

## What I Fixed

I fixed **two invoices** in transfer fee 502 that had SST discrepancies:

1. **Invoice DP20001286**
   - Expected: 103.89 + 42.40 = **146.29**
   - Was showing: **146.30** ❌
   - Fixed: Updated `transfer_fee_details.sst_amount` from 103.90 → 103.89

2. **Invoice DP20001272**
   - Expected: 49.09 + 67.20 = **116.29**
   - Was showing: **116.30** ❌
   - Fixed: Updated `transfer_fee_details.sst_amount` from 49.10 → 49.09

---

## Is This a Calculation Issue or Data Issue?

### **This is a DATA ISSUE, NOT a calculation issue.**

### Why?

The "Transferred SST" column **does NOT calculate** `sst + reimb sst` from the invoice table.

Instead, it displays values that were **stored in the `transfer_fee_details` table** when the transfer was created.

### How It Works

```
┌─────────────────────────────────────────────────────────┐
│  Invoice Table (loan_case_invoice_main)                  │
│  ├─ sst_inv: 49.09                                       │
│  └─ reimbursement_sst: 67.20                             │
│     Expected Total: 116.29                                │
└─────────────────────────────────────────────────────────┘
                        ↓
        When transfer is created, values are copied to:
                        ↓
┌─────────────────────────────────────────────────────────┐
│  Transfer Fee Details Table (transfer_fee_details)       │
│  ├─ sst_amount: 49.10  ← Stored value (0.01 off!)       │
│  └─ reimbursement_sst_amount: 67.20                     │
│     Displayed Total: 116.30  ← Shows this value         │
└─────────────────────────────────────────────────────────┘
```

### The Code

Looking at `resources/views/dashboard/transfer-fee-v3/edit.blade.php` (lines 483-487):

```php
// Show amounts transferred in THIS transfer fee record only
$transferredSst = $detail->sst_amount ?? 0;  // ← From transfer_fee_details table
$transferredReimbursementSst = $detail->reimbursement_sst_amount ?? 0;  // ← From transfer_fee_details table
$transferredSstTotal = $transferredSst + $transferredReimbursementSst;
```

**It's reading from `transfer_fee_details`, NOT calculating from invoice values.**

---

## Root Cause

The discrepancy happens because:

1. **Rounding differences** when the transfer was initially created
   - The invoice might have been calculated as 49.09
   - But when stored in `transfer_fee_details`, it was rounded to 49.10

2. **Invoice values updated after transfer**
   - The invoice SST might have been corrected/updated after the transfer was created
   - The `transfer_fee_details` table wasn't updated to reflect the change

---

## Why This Matters

The "Transferred SST" column is meant to show **what was actually transferred**, not what the invoice currently shows. However, when there are rounding discrepancies, it can cause confusion.

---

## Solution

I updated the `transfer_fee_details` records to match the current invoice values:

```sql
UPDATE transfer_fee_details 
SET sst_amount = 49.09  -- Match invoice sst_inv
WHERE id = 9509;
```

---

## Prevention

The system **should** automatically update `transfer_fee_details` when invoice SST values change (see `InvoiceController::updateTransferFeeMainAmountsForInvoice()`), but:

1. This only works if the invoice is updated through the proper controller method
2. If invoices are updated directly in the database, the transfer_fee_details won't sync
3. Rounding differences can still occur during initial transfer creation

---

## Summary

- **Type**: Data issue (stored values don't match invoice values)
- **Not**: Calculation issue (the calculation is correct, it's just using old stored data)
- **Fix**: Update `transfer_fee_details` table to match current invoice values
- **Status**: ✅ Fixed for both invoices
